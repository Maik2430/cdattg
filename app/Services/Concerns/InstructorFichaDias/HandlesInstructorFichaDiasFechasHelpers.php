<?php

namespace App\Services\Concerns\InstructorFichaDias;

use Carbon\Carbon;

trait HandlesInstructorFichaDiasFechasHelpers
{
    /**
     * Genera las fechas efectivas de formación dentro del rango de la ficha.
     *
     * @param  InstructorFichaCaracterizacion|object  $instructorFicha
     */
    public function generarFechasEfectivas($instructorFicha, array $diasData): array
    {
        $fechasEfectivas = [];

        $fechaInicio = $instructorFicha->fecha_inicio ?? ($instructorFicha->ficha->fecha_inicio ?? null);
        $fechaFin = $instructorFicha->fecha_fin ?? ($instructorFicha->ficha->fecha_fin ?? null);

        if (! $fechaInicio || ! $fechaFin) {
            return [];
        }

        $fechaActual = Carbon::parse($fechaInicio);
        $fechaFinal = Carbon::parse($fechaFin);

        $diasSemanaMap = $this->mapearDiasANumerosSemana($diasData);

        while ($fechaActual->lte($fechaFinal)) {
            $diaSemana = $fechaActual->dayOfWeek;

            if (isset($diasSemanaMap[$diaSemana])) {
                $diaInfo = $diasSemanaMap[$diaSemana];

                $fechasEfectivas[] = [
                    'fecha' => $fechaActual->format('Y-m-d'),
                    'dia_semana' => $fechaActual->locale('es')->isoFormat('dddd'),
                    'dia_id' => $diaInfo['dia_id'],
                    'hora_inicio' => $diaInfo['hora_inicio'] ?? null,
                    'hora_fin' => $diaInfo['hora_fin'] ?? null,
                ];
            }

            $fechaActual->addDay();
        }

        return $fechasEfectivas;
    }

    /**
     * Mapea los IDs de días de parámetros a números de día de la semana.
     */
    protected function mapearDiasANumerosSemana(array $diasData): array
    {
        $mapa = [];

        $diaIdANumero = [
            12 => 1,
            13 => 2,
            14 => 3,
            15 => 4,
            16 => 5,
            17 => 6,
            18 => 0,
        ];

        foreach ($diasData as $diaData) {
            $diaId = $diaData['dia_id'];
            if (isset($diaIdANumero[$diaId])) {
                $numeroDia = $diaIdANumero[$diaId];
                $mapa[$numeroDia] = $diaData;
            }
        }

        return $mapa;
    }
}
