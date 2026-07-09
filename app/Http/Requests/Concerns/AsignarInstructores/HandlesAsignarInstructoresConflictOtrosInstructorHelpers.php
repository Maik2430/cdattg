<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

use App\Models\Instructor;
use App\Models\InstructorFichaCaracterizacion;
use Carbon\Carbon;

trait HandlesAsignarInstructoresConflictOtrosInstructorHelpers
{
    private function validarConflictosOtrosInstructor($validator, $instructorId, $fechaInicio, $fechaFin, $diasNuevos, $jornadaIdFicha, $index): void
    {
        $instructorData = $this->input("instructores.{$index}", []);

        $horariosNuevos = [];
        if (isset($instructorData['dias']) && is_array($instructorData['dias'])) {
            foreach ($instructorData['dias'] as $diaId => $diaInfo) {
                if (isset($diaInfo['hora_inicio']) && isset($diaInfo['hora_fin'])) {
                    $horariosNuevos[$diaId] = [
                        'hora_inicio' => $diaInfo['hora_inicio'],
                        'hora_fin' => $diaInfo['hora_fin'],
                    ];
                }
            }
        }

        $conflictosQuery = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
            ->whereHas('ficha', function ($q) use ($jornadaIdFicha) {
                $q->where('status', true);

                if ($jornadaIdFicha) {
                    $q->where('jornada_id', $jornadaIdFicha);
                }
            })
            ->where(function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                    ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                    ->orWhere(function ($subQ) use ($fechaInicio, $fechaFin) {
                        $subQ->where('fecha_inicio', '<=', $fechaInicio)
                            ->where('fecha_fin', '>=', $fechaFin);
                    });
            })
            ->with(['ficha.jornadaFormacion.parametro', 'instructorFichaDias.dia']);

        $conflictosExistentes = $conflictosQuery->get();

        if (! empty($diasNuevos)) {
            $conflictosExistentes = $conflictosExistentes->filter(function ($conflicto) use ($diasNuevos, $horariosNuevos) {
                $diasExistentes = $conflicto->instructorFichaDias->pluck('dia_id')->toArray();
                $diasEnComun = array_intersect($diasNuevos, $diasExistentes);

                if (empty($diasEnComun)) {
                    return false;
                }

                if (! empty($horariosNuevos)) {
                    foreach ($diasEnComun as $diaId) {
                        if (! isset($horariosNuevos[$diaId])) {
                            continue;
                        }

                        $horaInicioNueva = $horariosNuevos[$diaId]['hora_inicio'];
                        $horaFinNueva = $horariosNuevos[$diaId]['hora_fin'];

                        $diaExistente = $conflicto->instructorFichaDias->firstWhere('dia_id', $diaId);
                        if ($diaExistente && $diaExistente->hora_inicio && $diaExistente->hora_fin) {
                            if ($this->hayConflictoHorario($horaInicioNueva, $horaFinNueva, $diaExistente->hora_inicio, $diaExistente->hora_fin)) {
                                return true;
                            }
                        }
                    }

                    return false;
                }

                return true;
            });
        }

        if ($conflictosExistentes->isNotEmpty()) {
            $instructor = Instructor::find($instructorId);
            $conflictosText = $conflictosExistentes->map(function ($conflicto) use ($diasNuevos, $horariosNuevos) {
                $programaNombre = $conflicto->ficha->programaFormacion->nombre ?? 'Sin programa';
                $jornada = $conflicto->ficha->jornadaFormacion->parametro->name ?? 'Sin jornada';

                $diasExistentes = $conflicto->instructorFichaDias->pluck('dia_id')->toArray();
                $diasEnComun = array_intersect($diasNuevos, $diasExistentes);
                $diasNombres = $conflicto->instructorFichaDias
                    ->whereIn('dia_id', $diasEnComun)
                    ->map(function ($dia) use ($horariosNuevos) {
                        $nombre = $dia->dia->name ?? '';
                        $horario = '';
                        if (isset($horariosNuevos[$dia->dia_id])) {
                            $horario = " ({$horariosNuevos[$dia->dia_id]['hora_inicio']}-{$horariosNuevos[$dia->dia_id]['hora_fin']})";
                        }
                        if ($dia->hora_inicio && $dia->hora_fin) {
                            $horario .= " [Conflicto: {$dia->hora_inicio}-{$dia->hora_fin}]";
                        }

                        return $nombre.$horario;
                    })
                    ->filter()
                    ->implode(', ');

                $diasInfo = $diasNombres ? " - Días en conflicto: {$diasNombres}" : '';

                return "Ficha {$conflicto->ficha->ficha} ({$programaNombre}) - Jornada: {$jornada}{$diasInfo} del ".Carbon::parse($conflicto->fecha_inicio)->format('d/m/Y').' al '.Carbon::parse($conflicto->fecha_fin)->format('d/m/Y');
            })->implode(', ');

            $validator->errors()->add(
                "instructores.{$index}.fecha_inicio",
                "📅 El instructor {$instructor->nombre_completo} ya tiene fichas con fechas superpuestas en la misma jornada, días y horarios: {$conflictosText}. Ajuste las fechas, jornada, días u horarios para evitar conflictos."
            );
        }
    }

    private function hayConflictoHorario(string $inicio1, string $fin1, string $inicio2, string $fin2): bool
    {
        $inicio1 = Carbon::parse($inicio1);
        $fin1 = Carbon::parse($fin1);
        $inicio2 = Carbon::parse($inicio2);
        $fin2 = Carbon::parse($fin2);

        return ! ($fin1->lte($inicio2) || $inicio1->gte($fin2));
    }
}
