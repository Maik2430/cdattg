<?php

namespace App\Services\Concerns\AsignacionInstructor;

use App\Models\AsignacionInstructorLog;
use App\Models\Instructor;
use App\Models\InstructorFichaCaracterizacion;
use Carbon\Carbon;

trait HandlesAsignacionInstructorEstadisticasActions
{
    public function obtenerEstadisticasAsignaciones(?Carbon $fechaInicio = null, ?Carbon $fechaFin = null): array
    {
        $fechaInicio = $fechaInicio ?? now()->startOfMonth();
        $fechaFin = $fechaFin ?? now()->endOfMonth();

        $estadisticas = AsignacionInstructorLog::obtenerEstadisticas($fechaInicio, $fechaFin);

        $totalAsignacionesActivas = InstructorFichaCaracterizacion::whereHas('ficha', function ($q) {
            $q->where('status', true)
                ->where('fecha_fin', '>=', now()->toDateString());
        })->count();

        $instructoresConFichas = Instructor::whereHas('instructorFichas', function ($q) {
            $q->whereHas('ficha', function ($subQ) {
                $subQ->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            });
        })->count();

        return array_merge($estadisticas, [
            'total_asignaciones_activas' => $totalAsignacionesActivas,
            'instructores_con_fichas' => $instructoresConFichas,
            'promedio_fichas_por_instructor' => $instructoresConFichas > 0
                ? round($totalAsignacionesActivas / $instructoresConFichas, 2)
                : 0,
        ]);
    }
}
