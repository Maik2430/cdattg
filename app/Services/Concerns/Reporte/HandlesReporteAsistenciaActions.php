<?php

namespace App\Services\Concerns\Reporte;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HandlesReporteAsistenciaActions
{
    /**
     * @return mixed
     */
    public function generarReporteAsistencia(int $fichaId, string $fechaInicio, string $fechaFin, string $formato = 'array')
    {
        try {
            $asistencias = $this->asistenciaRepo->obtenerPorFichaYFechas($fichaId, $fechaInicio, $fechaFin);
            $estadisticas = $this->asistenciaRepo->obtenerEstadisticas($fichaId, $fechaInicio, $fechaFin);
            $ficha = $this->fichaRepo->encontrarConRelaciones($fichaId);

            $datos = [
                'ficha' => [
                    'numero' => $ficha->ficha ?? 'N/A',
                    'programa' => $ficha->programaFormacion->nombre ?? 'N/A',
                    'jornada' => $ficha->jornadaFormacion->parametro->name ?? 'N/A',
                ],
                'periodo' => [
                    'inicio' => $fechaInicio,
                    'fin' => $fechaFin,
                ],
                'estadisticas' => $estadisticas,
                'asistencias' => $asistencias,
                'resumen_por_aprendiz' => $this->calcularResumenPorAprendiz($asistencias),
            ];

            return match ($formato) {
                'excel' => $this->generarExcel($datos),
                'pdf' => $this->generarPDF($datos),
                default => $datos,
            };
        } catch (\Exception $e) {
            Log::error('Error generando reporte de asistencia', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function generarReporteConsolidadoMes(int $mes, int $anio): array
    {
        try {
            $fechaInicio = Carbon::create($anio, $mes, 1)->startOfMonth()->format('Y-m-d');
            $fechaFin = Carbon::create($anio, $mes, 1)->endOfMonth()->format('Y-m-d');

            $fichas = $this->fichaRepo->obtenerVigentes();
            $reportes = [];

            foreach ($fichas as $ficha) {
                $estadisticas = $this->asistenciaRepo->obtenerEstadisticas($ficha->id, $fechaInicio, $fechaFin);

                $reportes[] = [
                    'ficha' => $ficha->ficha,
                    'programa' => $ficha->programaFormacion->nombre ?? 'N/A',
                    'estadisticas' => $estadisticas,
                ];
            }

            return [
                'periodo' => [
                    'mes' => $mes,
                    'anio' => $anio,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                ],
                'total_fichas' => count($reportes),
                'fichas' => $reportes,
            ];
        } catch (\Exception $e) {
            Log::error('Error generando reporte consolidado', [
                'mes' => $mes,
                'anio' => $anio,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
