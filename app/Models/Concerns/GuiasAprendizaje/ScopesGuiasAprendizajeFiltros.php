<?php

namespace App\Models\Concerns\GuiasAprendizaje;

use Illuminate\Database\Eloquent\Builder;

trait ScopesGuiasAprendizajeFiltros
{
    /**
     * Filtrar por código.
     */
    public function scopePorCodigo(Builder $query, string $codigo): Builder
    {
        return $query->where('codigo', 'LIKE', "%{$codigo}%");
    }

    /**
     * Filtrar por nombre.
     */
    public function scopePorNombre(Builder $query, string $nombre): Builder
    {
        return $query->where('nombre', 'LIKE', "%{$nombre}%");
    }

    /**
     * Filtrar por usuario creador.
     */
    public function scopePorUsuarioCreador(Builder $query, int $userId): Builder
    {
        return $query->where('user_create_id', $userId);
    }

    /**
     * Filtrar por fecha de creación.
     */
    public function scopePorFechaCreacion(Builder $query, string $fechaInicio, ?string $fechaFin = null): Builder
    {
        $query->whereDate('created_at', '>=', $fechaInicio);

        if ($fechaFin) {
            $query->whereDate('created_at', '<=', $fechaFin);
        }

        return $query;
    }
}
