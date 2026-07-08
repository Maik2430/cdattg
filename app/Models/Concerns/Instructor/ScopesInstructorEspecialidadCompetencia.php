<?php

namespace App\Models\Concerns\Instructor;

use Illuminate\Database\Eloquent\Builder;

trait ScopesInstructorEspecialidadCompetencia
{
    /**
     * Scope para buscar por especialidad.
     */
    public function scopePorEspecialidad(Builder $query, string $especialidad): Builder
    {
        return $query->whereJsonContains('especialidades', $especialidad);
    }

    /**
     * Scope para buscar por competencia.
     */
    public function scopePorCompetencia(Builder $query, string $competencia): Builder
    {
        return $query->whereJsonContains('competencias', $competencia);
    }

    /**
     * Scope para filtrar por años de experiencia mínimos.
     */
    public function scopeConExperienciaMinima(Builder $query, int $anosMinimos): Builder
    {
        return $query->where('anos_experiencia', '>=', $anosMinimos);
    }
}
