<?php

namespace App\Models\Concerns\Instructor;

use Illuminate\Database\Eloquent\Builder;

trait ScopesInstructorFiltros
{
    /**
     * Scope para filtrar por regional.
     */
    public function scopePorRegional(Builder $query, int $regionalId): Builder
    {
        return $query->where('regional_id', $regionalId);
    }

    /**
     * Scope para buscar por nombre o documento.
     */
    public function scopeBuscar(Builder $query, string $termino): Builder
    {
        return $query->whereHas('persona', function ($q) use ($termino) {
            $q->where('primer_nombre', 'like', "%{$termino}%")
                ->orWhere('segundo_nombre', 'like', "%{$termino}%")
                ->orWhere('primer_apellido', 'like', "%{$termino}%")
                ->orWhere('segundo_apellido', 'like', "%{$termino}%")
                ->orWhere('numero_documento', 'like', "%{$termino}%")
                ->orWhere('email', 'like', "%{$termino}%");
        });
    }

    /**
     * Scope para instructores con fichas asignadas.
     */
    public function scopeConFichas(Builder $query): Builder
    {
        return $query->whereHas('fichas');
    }

    /**
     * Scope para instructores sin fichas asignadas.
     */
    public function scopeSinFichas(Builder $query): Builder
    {
        return $query->whereDoesntHave('fichas');
    }

    /**
     * Scope para ordenar por nombre completo.
     */
    public function scopeOrdenarPorNombre(Builder $query, string $direccion = 'asc'): Builder
    {
        return $query->join('personas', 'instructors.persona_id', '=', 'personas.id')
            ->orderBy('personas.primer_nombre', $direccion)
            ->orderBy('personas.primer_apellido', $direccion)
            ->select('instructors.*');
    }
}
