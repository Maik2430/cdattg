<?php

namespace App\Models\Concerns\Asistencia;

use Illuminate\Database\Eloquent\Builder;

trait ScopesAsistencia
{
    /**
     * Scope para asistencias activas (no finalizadas)
     */
    public function scopeActiva(Builder $query): Builder
    {
        return $query->where('is_finished', false);
    }

    /**
     * Scope para asistencias finalizadas
     */
    public function scopeFinalizada(Builder $query): Builder
    {
        return $query->where('is_finished', true);
    }

    /**
     * Scope para asistencias de una ficha específica
     */
    public function scopeDeFicha(Builder $query, int $fichaId): Builder
    {
        return $query->where('instructor_ficha_id', $fichaId);
    }

    /**
     * Scope para asistencias de una evidencia específica
     */
    public function scopeDeEvidencia(Builder $query, int $evidenciaId): Builder
    {
        return $query->where('evidencia_id', $evidenciaId);
    }

    /**
     * Scope para asistencias de hoy
     */
    public function scopeHoy(Builder $query): Builder
    {
        return $query->whereDate('fecha', today());
    }
}
