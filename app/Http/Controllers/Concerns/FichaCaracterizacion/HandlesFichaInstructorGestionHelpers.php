<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

trait HandlesFichaInstructorGestionHelpers
{
    private function recalcularHorasInstructoresAsignados($instructoresAsignados): void
    {
        $diasService = app(\App\Services\InstructorFichaDiasService::class);
        foreach ($instructoresAsignados as $asignacion) {
            if ($asignacion->instructorFichaDias && $asignacion->instructorFichaDias->count() > 0) {
                $diasData = $asignacion->instructorFichaDias->map(function ($dia) {
                    return [
                        'dia_id' => $dia->dia_id,
                        'hora_inicio' => $dia->hora_inicio,
                        'hora_fin' => $dia->hora_fin,
                    ];
                })->toArray();

                $fechasEfectivas = $diasService->generarFechasEfectivas($asignacion, $diasData);
                $horasTotales = 0;
                foreach ($fechasEfectivas as $fecha) {
                    if (isset($fecha['hora_inicio']) && isset($fecha['hora_fin'])) {
                        $inicio = \Carbon\Carbon::parse($fecha['hora_inicio']);
                        $fin = \Carbon\Carbon::parse($fecha['hora_fin']);
                        if ($fin->lt($inicio)) {
                            $fin->addDay();
                        }
                        $diferencia = $inicio->diffInMinutes($fin);
                        $horasTotales += $diferencia / 60;
                    }
                }

                $horasTotales = round($horasTotales, 2);
                $asignacion->total_horas_instructor = $horasTotales;

                $horasOriginales = $asignacion->getOriginal('total_horas_instructor') ?? $asignacion->total_horas_instructor;
                if (abs($horasOriginales - $horasTotales) > 0.01) {
                    \DB::table('instructor_fichas_caracterizacion')
                        ->where('id', $asignacion->id)
                        ->update(['total_horas_instructor' => $horasTotales]);
                }
            }
        }
    }
}
