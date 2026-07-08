<?php

namespace App\Models\Concerns\FichaCaracterizacion;

use Illuminate\Database\Eloquent\Builder;

trait ScopesFichaCaracterizacionEstado
{
    /**
     * Scope para filtrar fichas activas.
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope para filtrar fichas inactivas.
     */
    public function scopeInactivas(Builder $query): Builder
    {
        return $query->where('status', false);
    }
}
