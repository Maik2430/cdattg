<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\InstructorFichaCaracterizacion;

trait HandlesFichaInstructorAvailabilityHelpers
{
    /**
     * Verifica la disponibilidad de instructores para una ficha.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $instructores
     * @param  \App\Models\FichaCaracterizacion  $ficha
     * @return array
     */
    private function verificarDisponibilidadInstructores($instructores, $ficha)
    {
        $disponibilidad = [];

        foreach ($instructores as $instructor) {
            // Verificar si el instructor ya está asignado a otras fichas en el mismo rango de fechas
            $fichasSuperpuestas = InstructorFichaCaracterizacion::where('instructor_id', $instructor->id)
                ->where('ficha_id', '!=', $ficha->id)
                ->where(function ($query) use ($ficha) {
                    $query->whereBetween('fecha_inicio', [$ficha->fecha_inicio, $ficha->fecha_fin])
                        ->orWhereBetween('fecha_fin', [$ficha->fecha_inicio, $ficha->fecha_fin])
                        ->orWhere(function ($subQuery) use ($ficha) {
                            $subQuery->where('fecha_inicio', '<=', $ficha->fecha_inicio)
                                ->where('fecha_fin', '>=', $ficha->fecha_fin);
                        });
                })
                ->count();

            $disponibilidad[$instructor->id] = [
                'disponible' => $fichasSuperpuestas == 0,
                'fichas_superpuestas' => $fichasSuperpuestas,
                'instructor' => $instructor,
            ];
        }

        return $disponibilidad;
    }
}
