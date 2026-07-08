<?php

namespace App\Models\Concerns\GuiasAprendizaje;

use Illuminate\Database\Eloquent\Builder;

trait ScopesGuiasAprendizajeEstado
{
    /**
     * Filtrar guías activas.
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    /**
     * Filtrar guías inactivas.
     */
    public function scopeInactivas(Builder $query): Builder
    {
        return $query->where('status', 0);
    }
}
