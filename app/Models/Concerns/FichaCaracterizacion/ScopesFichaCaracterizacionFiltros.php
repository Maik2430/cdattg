<?php

namespace App\Models\Concerns\FichaCaracterizacion;

use Illuminate\Database\Eloquent\Builder;

trait ScopesFichaCaracterizacionFiltros
{
    /**
     * Scope para filtrar fichas por programa de formación.
     */
    public function scopePorPrograma(Builder $query, int $programaId): Builder
    {
        return $query->where('programa_formacion_id', $programaId);
    }

    /**
     * Scope para filtrar fichas por sede.
     */
    public function scopePorSede(Builder $query, int $sedeId): Builder
    {
        return $query->where('sede_id', $sedeId);
    }

    /**
     * Scope para filtrar fichas por instructor.
     */
    public function scopePorInstructor(Builder $query, int $instructorId): Builder
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope para filtrar fichas por modalidad de formación.
     */
    public function scopePorModalidad(Builder $query, int $modalidadId): Builder
    {
        return $query->where('modalidad_formacion_id', $modalidadId);
    }

    /**
     * Scope para filtrar fichas por jornada.
     */
    public function scopePorJornada(Builder $query, int $jornadaId): Builder
    {
        return $query->where('jornada_id', $jornadaId);
    }

    /**
     * Scope para filtrar fichas que tienen aprendices.
     */
    public function scopeConAprendices(Builder $query): Builder
    {
        return $query->has('aprendices');
    }

    /**
     * Scope para filtrar fichas sin aprendices.
     */
    public function scopeSinAprendices(Builder $query): Builder
    {
        return $query->doesntHave('aprendices');
    }
}
