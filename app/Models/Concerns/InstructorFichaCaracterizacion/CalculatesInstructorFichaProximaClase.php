<?php

namespace App\Models\Concerns\InstructorFichaCaracterizacion;

use Carbon\Carbon;

trait CalculatesInstructorFichaProximaClase
{
    /**
     * Obtiene la próxima clase basada en la fecha y hora actual
     *
     * @return array|null Array con 'hora_inicio', 'hora_fin', 'dia_nombre', 'fecha_proxima' o null si no hay próxima clase
     */
    public function obtenerProximaClase()
    {
        $horaActual = now();
        $diaActual = $horaActual->dayOfWeek;

        $diaIdActual = ($diaActual == 0) ? 18 : $diaActual + 11;

        $diasFormacion = $this->instructorFichaDias()
            ->orderBy('dia_id')
            ->get();

        if ($diasFormacion->isEmpty()) {
            return null;
        }

        $claseHoy = $diasFormacion->where('dia_id', $diaIdActual)->first();

        if ($claseHoy) {
            $horaFin = Carbon::parse($claseHoy->hora_fin);

            if ($horaActual->greaterThan($horaFin)) {
                return $this->buscarProximaClaseSemana($diasFormacion, $diaActual);
            }

            return [
                'hora_inicio' => $claseHoy->hora_inicio,
                'hora_fin' => $claseHoy->hora_fin,
                'dia_nombre' => $this->obtenerNombreDia($claseHoy->dia_id),
                'dia_id' => $claseHoy->dia_id,
                'fecha_proxima' => $horaActual->format('Y-m-d'),
                'es_hoy' => true,
            ];
        }

        return $this->buscarProximaClaseSemana($diasFormacion, $diaActual);
    }

    /**
     * Busca la próxima clase en la semana
     */
    private function buscarProximaClaseSemana($diasFormacion, $diaActual)
    {
        $diasSemana = [1, 2, 3, 4, 5, 6, 0];
        $diaActualIndex = array_search($diaActual, $diasSemana);

        for ($i = 1; $i <= 7; $i++) {
            $diaIndex = ($diaActualIndex + $i) % 7;
            $diaSemana = $diasSemana[$diaIndex];
            $diaId = ($diaSemana == 0) ? 18 : $diaSemana + 11;

            $clase = $diasFormacion->where('dia_id', $diaId)->first();

            if ($clase) {
                $fechaProxima = now()->addDays($i);

                return [
                    'hora_inicio' => $clase->hora_inicio,
                    'hora_fin' => $clase->hora_fin,
                    'dia_nombre' => $this->obtenerNombreDia($clase->dia_id),
                    'dia_id' => $clase->dia_id,
                    'fecha_proxima' => $fechaProxima->format('Y-m-d'),
                    'es_hoy' => false,
                    'dias_restantes' => $i,
                ];
            }
        }

        return null;
    }

    /**
     * Obtiene el nombre del día basado en el ID
     */
    private function obtenerNombreDia($diaId)
    {
        $dias = [
            12 => 'LUNES',
            13 => 'MARTES',
            14 => 'MIERCOLES',
            15 => 'JUEVES',
            16 => 'VIERNES',
            17 => 'SÁBADO',
            18 => 'DDOMINGO',
        ];

        return $dias[$diaId] ?? 'Desconocido';
    }
}
