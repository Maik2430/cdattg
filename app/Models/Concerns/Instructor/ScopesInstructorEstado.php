<?php

namespace App\Models\Concerns\Instructor;

use Illuminate\Database\Eloquent\Builder;

trait ScopesInstructorEstado
{
    /**
     * Scope para instructores activos.
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope para instructores inactivos.
     */
    public function scopeInactivos(Builder $query): Builder
    {
        return $query->where('status', false);
    }
}
