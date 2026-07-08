<?php

namespace App\Services\Concerns\AsignacionInstructor;

use App\Models\FichaCaracterizacion;
use App\Services\InstructorFichaDiasService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HandlesAsignacionInstructorHorasHelpers
{
    public function calcularHorasTotalesAutomaticas(array $instructorData, int $fichaId): int
    {
        try {
            $ficha = FichaCaracterizacion::with(['diasFormacion', 'jornadaFormacion'])->findOrFail($fichaId);

            $diasSeleccionados = $this->extraerDiasSeleccionados($instructorData);

            if (empty($diasSeleccionados)) {
                return 40;
            }

            $diasParaServicio = [];
            foreach ($diasSeleccionados as $diaId) {
                $diaFormacionFicha = $ficha->diasFormacion->firstWhere('dia_id', $diaId);

                $diasParaServicio[] = [
                    'dia_id' => $diaId,
                    'hora_inicio' => $diaFormacionFicha->hora_inicio ?? ($ficha->jornadaFormacion->hora_inicio ?? '08:00'),
                    'hora_fin' => $diaFormacionFicha->hora_fin ?? ($ficha->jornadaFormacion->hora_fin ?? '12:00'),
                ];
            }

            $instructorFichaTemp = new \stdClass;
            $instructorFichaTemp->fecha_inicio = $instructorData['fecha_inicio'];
            $instructorFichaTemp->fecha_fin = $instructorData['fecha_fin'];
            $instructorFichaTemp->ficha = $ficha;

            return (int) round($this->calcularHorasDesdeFechasEfectivas($instructorFichaTemp, $diasParaServicio));
        } catch (\Exception $e) {
            Log::error('Error calculando horas automáticas, se usa el valor por defecto', [
                'ficha_id' => $fichaId,
                'instructor_data' => $instructorData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 40;
        }
    }

    protected function extraerDiasSeleccionados(array $instructorData): array
    {
        if (isset($instructorData['dias_semana']) && is_array($instructorData['dias_semana'])) {
            return $instructorData['dias_semana'];
        }

        if (isset($instructorData['dias']) && is_array($instructorData['dias'])) {
            return array_keys($instructorData['dias']);
        }

        if (isset($instructorData['dias_formacion']) && is_array($instructorData['dias_formacion'])) {
            return collect($instructorData['dias_formacion'])->pluck('dia_id')->filter()->toArray();
        }

        return [];
    }

    protected function calcularHorasDesdeFechasEfectivas($instructorFicha, array $diasData): int
    {
        try {
            $diasService = app(InstructorFichaDiasService::class);
            $fechasEfectivas = $diasService->generarFechasEfectivas($instructorFicha, $diasData);

            $totalHoras = 0;
            foreach ($fechasEfectivas as $fecha) {
                if ($fecha['hora_inicio'] && $fecha['hora_fin']) {
                    $totalHoras += $this->convertirTiempoAHoras($fecha['hora_inicio'], $fecha['hora_fin']);
                }
            }

            Log::info('Horas calculadas desde fechas efectivas', [
                'total_fechas' => count($fechasEfectivas),
                'total_horas' => $totalHoras,
            ]);

            return (int) $totalHoras;
        } catch (\Exception $e) {
            Log::error('Error al calcular horas desde fechas efectivas', [
                'error' => $e->getMessage(),
            ]);

            return 40;
        }
    }

    protected function convertirTiempoAHoras(?string $horaInicio, ?string $horaFin): float
    {
        if (! $horaInicio || ! $horaFin) {
            Log::warning('Horas de jornada no definidas, usando valor por defecto', [
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'valor_defecto' => 6.5,
            ]);

            return 6.5;
        }

        try {
            $inicio = Carbon::parse($horaInicio);
            $fin = Carbon::parse($horaFin);

            return $inicio->diffInMinutes($fin) / 60;
        } catch (\Exception $e) {
            Log::error('Error parseando horas de jornada', [
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'error' => $e->getMessage(),
            ]);

            return 6.5;
        }
    }
}
