<?php

namespace App\Models\Concerns\GuiasAprendizaje;

use Illuminate\Database\Eloquent\Builder;

trait ScopesGuiasAprendizajeOrdenamiento
{
    /**
     * Ordenar por fecha de creación descendente.
     */
    public function scopeMasRecientes(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Ordenar por nombre ascendente.
     */
    public function scopePorNombreAsc(Builder $query): Builder
    {
        return $query->orderBy('nombre', 'asc');
    }
}
