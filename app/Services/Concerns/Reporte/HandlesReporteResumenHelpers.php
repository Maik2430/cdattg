<?php

namespace App\Services\Concerns\Reporte;

trait HandlesReporteResumenHelpers
{
    protected function calcularResumenPorAprendiz($asistencias): array
    {
        $resumen = [];

        foreach ($asistencias as $asistencia) {
            $documento = $asistencia->numero_identificacion;

            if (! isset($resumen[$documento])) {
                $resumen[$documento] = [
                    'nombres' => $asistencia->nombres,
                    'apellidos' => $asistencia->apellidos,
                    'total_asistencias' => 0,
                    'llegadas_tarde' => 0,
                    'salidas_anticipadas' => 0,
                ];
            }

            $resumen[$documento]['total_asistencias']++;

            if ($asistencia->novedad_entrada === 'Tarde' || $asistencia->novedad_entrada === 'Muy tarde') {
                $resumen[$documento]['llegadas_tarde']++;
            }

            if ($asistencia->novedad_salida === 'Anticipada') {
                $resumen[$documento]['salidas_anticipadas']++;
            }
        }

        return $resumen;
    }
}
