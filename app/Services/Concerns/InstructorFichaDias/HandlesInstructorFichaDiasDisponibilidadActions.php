<?php

namespace App\Services\Concerns\InstructorFichaDias;

use App\Models\InstructorFichaCaracterizacion;
use App\Models\InstructorFichaDias;
use Carbon\Carbon;

trait HandlesInstructorFichaDiasDisponibilidadActions
{
    /**
     * Valida la disponibilidad del instructor en los días y horarios especificados.
     * Valida: fechas, jornada, días y horarios
     */
    public function validarDisponibilidadInstructor(InstructorFichaCaracterizacion $instructorFicha, array $diasData): array
    {
        $conflictos = [];

        $fechaInicio = Carbon::parse($instructorFicha->fecha_inicio);
        $fechaFin = Carbon::parse($instructorFicha->fecha_fin);
        $jornadaIdFicha = $instructorFicha->ficha->jornada_id ?? null;
        $diasIdsNuevos = collect($diasData)->pluck('dia_id')->toArray();

        $otrasAsignacionesFicha = InstructorFichaCaracterizacion::where('instructor_id', $instructorFicha->instructor_id)
            ->where('id', '!=', $instructorFicha->id)
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
            ->with(['ficha.jornadaFormacion.parametro', 'instructorFichaDias.dia'])
            ->get();

        foreach ($diasData as $diaData) {
            $diaId = $diaData['dia_id'];
            $horaInicio = $diaData['hora_inicio'] ?? null;
            $horaFin = $diaData['hora_fin'] ?? null;

            foreach ($otrasAsignacionesFicha as $otraAsignacionFicha) {
                $diaExistente = $otraAsignacionFicha->instructorFichaDias->firstWhere('dia_id', $diaId);

                if ($diaExistente) {
                    $hayConflictoHorario = false;

                    if ($horaInicio && $horaFin && $diaExistente->hora_inicio && $diaExistente->hora_fin) {
                        $hayConflictoHorario = $this->hayConflictoHorario(
                            $horaInicio,
                            $horaFin,
                            $diaExistente->hora_inicio,
                            $diaExistente->hora_fin
                        );
                    } elseif ($horaInicio && $horaFin && (! $diaExistente->hora_inicio || ! $diaExistente->hora_fin)) {
                        $hayConflictoHorario = true;
                    } elseif (! $horaInicio || ! $horaFin) {
                        $hayConflictoHorario = true;
                    }

                    if ($hayConflictoHorario) {
                        $conflictos[] = [
                            'dia_id' => $diaId,
                            'dia_nombre' => $this->obtenerNombreDia($diaId),
                            'ficha_conflicto' => $otraAsignacionFicha->ficha->ficha ?? 'N/A',
                            'programa_conflicto' => $otraAsignacionFicha->ficha->programaFormacion->nombre ?? 'N/A',
                            'jornada_conflicto' => $otraAsignacionFicha->ficha->jornadaFormacion->parametro->name ?? 'N/A',
                            'fecha_inicio_conflicto' => Carbon::parse($otraAsignacionFicha->fecha_inicio)->format('d/m/Y'),
                            'fecha_fin_conflicto' => Carbon::parse($otraAsignacionFicha->fecha_fin)->format('d/m/Y'),
                            'horario_conflicto' => $diaExistente->hora_inicio && $diaExistente->hora_fin
                                ? $diaExistente->hora_inicio.' - '.$diaExistente->hora_fin
                                : 'Sin horario',
                            'horario_solicitado' => $horaInicio && $horaFin
                                ? $horaInicio.' - '.$horaFin
                                : 'Sin horario',
                        ];
                    }
                }
            }
        }

        return [
            'disponible' => empty($conflictos),
            'conflictos' => $conflictos,
        ];
    }

    /**
     * Verifica si un instructor está disponible en un día y horario específico.
     */
    public function estaDisponible(int $instructorId, int $diaId, ?string $horaInicio = null, ?string $horaFin = null, ?int $excludeInstructorFichaId = null): bool
    {
        $query = InstructorFichaDias::whereHas('instructorFicha', function ($q) use ($instructorId, $excludeInstructorFichaId) {
            $q->where('instructor_id', $instructorId);
            if ($excludeInstructorFichaId) {
                $q->where('id', '!=', $excludeInstructorFichaId);
            }
        })->where('dia_id', $diaId);

        if (! $horaInicio || ! $horaFin) {
            return $query->count() == 0;
        }

        $asignaciones = $query->get();

        foreach ($asignaciones as $asignacion) {
            if ($asignacion->hora_inicio && $asignacion->hora_fin) {
                if ($this->hayConflictoHorario($horaInicio, $horaFin, $asignacion->hora_inicio, $asignacion->hora_fin)) {
                    return false;
                }
            }
        }

        return true;
    }
}
