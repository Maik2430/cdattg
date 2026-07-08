<?php

namespace App\Models\Concerns\Competencia;

trait ScopesCompetencia
{
    public function scopeActivos($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactivos($query)
    {
        return $query->where('status', 0);
    }

    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', $codigo);
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->where(function ($q) use ($fechaInicio, $fechaFin) {
            $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin]);
        });
    }

    public function scopeVigentes($query)
    {
        return $query->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->where('status', 1);
    }

    public function scopeOrdenadoPorCodigo($query)
    {
        return $query->orderBy('codigo', 'asc');
    }

    public function scopePorPrograma($query, $programaId)
    {
        return $query->whereHas('programasFormacion', function ($q) use ($programaId) {
            $q->where('programas_formacion.id', $programaId);
        });
    }
}
