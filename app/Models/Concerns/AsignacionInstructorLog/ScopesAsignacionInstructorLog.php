<?php

namespace App\Models\Concerns\AsignacionInstructorLog;

use Carbon\Carbon;

trait ScopesAsignacionInstructorLog
{
    /**
     * Scope para filtrar por resultado
     */
    public function scopeExitoso($query)
    {
        return $query->where('resultado', 'exitoso');
    }

    /**
     * Scope para filtrar por error
     */
    public function scopeConError($query)
    {
        return $query->where('resultado', 'error');
    }

    /**
     * Scope para filtrar por acción
     */
    public function scopeAccion($query, string $accion)
    {
        return $query->where('accion', $accion);
    }

    /**
     * Scope para filtrar por instructor
     */
    public function scopeInstructor($query, int $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope para filtrar por ficha
     */
    public function scopeFicha($query, int $fichaId)
    {
        return $query->where('ficha_id', $fichaId);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, Carbon $fechaInicio, Carbon $fechaFin)
    {
        return $query->whereBetween('fecha_accion', [$fechaInicio, $fechaFin]);
    }
}
