<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use Illuminate\Support\Facades\Log;

trait HandlesFichaDiasFormacionHelpers
{
    /**
     * Obtiene la configuración de jornadas y días permitidos.
     *
     * @return array
     */
    private function obtenerConfiguracionJornadas()
    {
        return [
            1 => [ // MAÑANA
                'nombre' => 'MAÑANA',
                'dias_permitidos' => [12, 13, 14, 15, 16, 17], // LUNES a SÁBADO
                'horario_tipico' => ['08:00', '12:00'],
            ],
            2 => [ // TARDE
                'nombre' => 'TARDE',
                'dias_permitidos' => [12, 13, 14, 15, 16, 17], // LUNES a SÁBADO
                'horario_tipico' => ['14:00', '18:00'],
            ],
            3 => [ // NOCHE
                'nombre' => 'NOCHE',
                'dias_permitidos' => [12, 13, 14, 15, 16, 17], // LUNES a SÁBADO
                'horario_tipico' => ['18:00', '22:00'],
            ],
            4 => [ // FIN DE SEMANA
                'nombre' => 'FIN DE SEMANA',
                'dias_permitidos' => [17], // SÁBADO
                'horario_tipico' => ['08:00', '17:00'],
            ],
            5 => [ // MIXTA
                'nombre' => 'MIXTA',
                'dias_permitidos' => [12, 13, 14, 15, 16, 17], // LUNES a SÁBADO
                'horario_tipico' => ['08:00', '18:00'],
            ],
        ];
    }

    /**
     * Calcula las horas totales de formación basado en los días asignados.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $diasFormacion
     * @param  \App\Models\FichaCaracterizacion  $ficha
     * @return int
     */
    private function calcularHorasTotales($diasFormacion, $ficha)
    {
        $horasTotales = 0;
        $duracionEnDias = $ficha->duracionEnDias();

        foreach ($diasFormacion as $dia) {
            try {
                // Usar parse() en lugar de createFromFormat() para mayor flexibilidad
                $horaInicio = \Carbon\Carbon::parse($dia->hora_inicio);
                $horaFin = \Carbon\Carbon::parse($dia->hora_fin);
                $horasPorDia = $horaInicio->diffInHours($horaFin);

                $horasTotales += $horasPorDia * $duracionEnDias;
            } catch (\Exception $e) {
                // Log del error y continuar con el siguiente día
                Log::warning('Error al calcular horas para el día: '.$e->getMessage(), [
                    'dia_id' => $dia->id ?? 'N/A',
                    'hora_inicio' => $dia->hora_inicio ?? 'N/A',
                    'hora_fin' => $dia->hora_fin ?? 'N/A',
                ]);

                continue;
            }
        }

        return $horasTotales;
    }
}
