<?php

namespace App\Traits\ValidacionesSena;

use Carbon\Carbon;

trait ValidacionesSenaFechas
{
    /**
     * Valida que las fechas no coincidan con días festivos o vacaciones.
     *
     * @param  string  $fechaInicio  Fecha de inicio
     * @param  string  $fechaFin  Fecha de fin
     * @return array Resultado de la validación
     */
    protected function validarFechasFestivos($fechaInicio, $fechaFin)
    {
        try {
            $fechaInicio = Carbon::parse($fechaInicio);
            $fechaFin = Carbon::parse($fechaFin);

            // Días festivos fijos en Colombia (pueden ser configurados en BD)
            $festivosFijos = [
                '01-01', // Año Nuevo
                '01-06', // Reyes Magos
                '03-19', // San José
                '04-09', // Domingo de Ramos
                '04-10', // Jueves Santo
                '04-11', // Viernes Santo
                '05-01', // Día del Trabajo
                '05-13', // Día de la Ascensión
                '06-03', // Corpus Christi
                '06-10', // Sagrado Corazón
                '07-01', // San Pedro y San Pablo
                '07-20', // Día de la Independencia
                '08-07', // Batalla de Boyacá
                '08-19', // La Asunción
                '10-14', // Día de la Raza
                '11-04', // Todos los Santos
                '11-11', // Independencia de Cartagena
                '12-08', // Inmaculada Concepción
                '12-25',  // Navidad
            ];

            $fechasFestivas = [];

            // Verificar cada día del rango
            $fechaActual = $fechaInicio->copy();
            while ($fechaActual->lte($fechaFin)) {
                $diaMes = $fechaActual->format('m-d');

                if (in_array($diaMes, $festivosFijos)) {
                    $fechasFestivas[] = $fechaActual->format('d/m/Y');
                }

                $fechaActual->addDay();
            }

            if (! empty($fechasFestivas)) {
                return [
                    'valido' => false,
                    'mensaje' => 'Las fechas seleccionadas incluyen días festivos: '.implode(', ', $fechasFestivas).'. Se recomienda ajustar las fechas.',
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'Las fechas no coinciden con días festivos.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar fechas festivas: '.$e->getMessage(),
            ];
        }
    }
}
