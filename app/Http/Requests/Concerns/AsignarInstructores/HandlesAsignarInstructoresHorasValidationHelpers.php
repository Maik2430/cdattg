<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

use App\Models\Competencia;
use App\Models\FichaCaracterizacion;
use App\Models\ResultadosAprendizaje;
use App\Services\InstructorFichaDiasService;
use Carbon\Carbon;

trait HandlesAsignarInstructoresHorasValidationHelpers
{
    private function validarCoherenciaHorasCompetencia($validator): void
    {
        $instructores = $this->input('instructores', []);
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::with(['diasFormacion', 'jornadaFormacion.parametro'])->find($fichaId);

        if (! $ficha) {
            return;
        }

        foreach ($instructores as $index => $instructorData) {
            $competenciaId = $instructorData['competencia_id'] ?? null;
            $resultadosIds = $instructorData['resultados_aprendizaje'] ?? [];

            if (! $competenciaId && empty($resultadosIds)) {
                continue;
            }

            $horasTrabajadas = $this->calcularHorasTrabajadas($instructorData, $ficha);
            $duracionEsperada = 0;

            if (! empty($resultadosIds)) {
                $resultados = ResultadosAprendizaje::whereIn('id', $resultadosIds)->get();
                $duracionEsperada = $resultados->sum('duracion');
            } elseif ($competenciaId) {
                $competencia = Competencia::find($competenciaId);
                if ($competencia) {
                    $duracionEsperada = $competencia->duracion;
                }
            }

            if ($duracionEsperada <= 0) {
                continue;
            }

            $diferencia = abs($horasTrabajadas - $duracionEsperada);
            $porcentajeDiferencia = ($diferencia / $duracionEsperada) * 100;

            if ($porcentajeDiferencia > 10) {
                $competenciaNombre = $competenciaId
                    ? (Competencia::find($competenciaId)->nombre ?? 'Competencia')
                    : 'Resultados de aprendizaje';

                $validator->errors()->add(
                    "instructores.{$index}.fecha_inicio",
                    "⚠️ INCOHERENCIA DE HORAS: Las horas trabajadas ({$horasTrabajadas}h) no son coherentes con la duración esperada ({$duracionEsperada}h) de {$competenciaNombre}. Diferencia: {$diferencia}h ({$porcentajeDiferencia}%). Ajuste las fechas, días u horarios para que coincidan."
                );
            }
        }
    }

    private function calcularHorasTrabajadas(array $instructorData, FichaCaracterizacion $ficha): int
    {
        try {
            $fechaInicio = Carbon::parse($instructorData['fecha_inicio']);
            $fechaFin = Carbon::parse($instructorData['fecha_fin']);

            $diasSeleccionados = [];
            $diasConHorarios = [];

            if (isset($instructorData['dias']) && is_array($instructorData['dias'])) {
                foreach ($instructorData['dias'] as $diaId => $diaInfo) {
                    $diasSeleccionados[] = $diaId;
                    if (isset($diaInfo['hora_inicio']) && isset($diaInfo['hora_fin'])) {
                        $diasConHorarios[$diaId] = [
                            'hora_inicio' => $diaInfo['hora_inicio'],
                            'hora_fin' => $diaInfo['hora_fin'],
                        ];
                    }
                }
            } elseif (isset($instructorData['dias_semana']) && is_array($instructorData['dias_semana'])) {
                $diasSeleccionados = $instructorData['dias_semana'];
            } elseif (isset($instructorData['dias_formacion']) && is_array($instructorData['dias_formacion'])) {
                $diasSeleccionados = collect($instructorData['dias_formacion'])->pluck('dia_id')->filter()->toArray();
            }

            if (empty($diasSeleccionados)) {
                return 0;
            }

            $diasParaCalculo = [];
            foreach ($diasSeleccionados as $diaId) {
                if (isset($diasConHorarios[$diaId])) {
                    $diasParaCalculo[] = [
                        'dia_id' => $diaId,
                        'hora_inicio' => $diasConHorarios[$diaId]['hora_inicio'],
                        'hora_fin' => $diasConHorarios[$diaId]['hora_fin'],
                    ];
                } else {
                    $diaFormacionFicha = $ficha->diasFormacion->firstWhere('dia_id', $diaId);
                    $horaInicio = $diaFormacionFicha->hora_inicio ?? '08:00';
                    $horaFin = $diaFormacionFicha->hora_fin ?? '12:00';

                    $diasParaCalculo[] = [
                        'dia_id' => $diaId,
                        'hora_inicio' => $horaInicio,
                        'hora_fin' => $horaFin,
                    ];
                }
            }

            $instructorFichaTemp = new \stdClass;
            $instructorFichaTemp->fecha_inicio = $fechaInicio->format('Y-m-d');
            $instructorFichaTemp->fecha_fin = $fechaFin->format('Y-m-d');
            $instructorFichaTemp->ficha = $ficha;

            $diasService = app(InstructorFichaDiasService::class);
            $fechasEfectivas = $diasService->generarFechasEfectivas($instructorFichaTemp, $diasParaCalculo);

            $totalHoras = 0;
            foreach ($fechasEfectivas as $fecha) {
                if ($fecha['hora_inicio'] && $fecha['hora_fin']) {
                    $totalHoras += $this->convertirTiempoAHoras($fecha['hora_inicio'], $fecha['hora_fin']);
                }
            }

            return (int) round($totalHoras);
        } catch (\Exception $e) {
            \Log::error('Error calculando horas trabajadas en validación', [
                'error' => $e->getMessage(),
                'instructor_data' => $instructorData,
            ]);

            return 0;
        }
    }

    private function convertirTiempoAHoras(?string $horaInicio, ?string $horaFin): float
    {
        if (! $horaInicio || ! $horaFin) {
            return 0;
        }

        try {
            $inicio = Carbon::parse($horaInicio);
            $fin = Carbon::parse($horaFin);

            if ($fin->lt($inicio)) {
                $fin->addDay();
            }

            return $inicio->diffInMinutes($fin) / 60;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
