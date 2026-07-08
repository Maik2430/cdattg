<?php

namespace App\Models\Concerns\ResultadosAprendizaje;

trait ScopesResultadosAprendizaje
{
    /**
     * SCOPE: Filtrar resultados activos
     */
    public function scopeActivos($query)
    {
        return $query->where('status', 1);
    }

    /**
     * SCOPE: Filtrar resultados inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('status', 0);
    }

    /**
     * SCOPE: Filtrar por competencia
     */
    public function scopePorCompetencia($query, $competenciaId)
    {
        return $query->whereHas('competencias', function ($q) use ($competenciaId) {
            $q->where('competencias.id', $competenciaId);
        });
    }

    /**
     * SCOPE: Filtrar por código
     */
    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', 'LIKE', "%{$codigo}%");
    }

    /**
     * SCOPE: Ordenar por código ascendente
     */
    public function scopeOrdenadoPorCodigo($query)
    {
        return $query->orderBy('codigo', 'asc');
    }
}
