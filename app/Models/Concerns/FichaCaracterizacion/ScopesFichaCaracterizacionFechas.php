<?php

namespace App\Models\Concerns\FichaCaracterizacion;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait ScopesFichaCaracterizacionFechas
{
    /**
     * Scope para filtrar fichas por rango de fechas.
     */
    public function scopePorRangoFechas(Builder $query, string $fechaInicio, string $fechaFin): Builder
    {
        return $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
            ->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
                $q->where('fecha_inicio', '<=', $fechaInicio)
                    ->where('fecha_fin', '>=', $fechaFin);
            });
    }

    /**
     * Scope para filtrar fichas que están en curso actualmente.
     */
    public function scopeEnCurso(Builder $query): Builder
    {
        $hoy = Carbon::today();

        return $query->where('fecha_inicio', '<=', $hoy)
            ->where('fecha_fin', '>=', $hoy);
    }

    /**
     * Scope para filtrar fichas que han terminado.
     */
    public function scopeTerminadas(Builder $query): Builder
    {
        return $query->where('fecha_fin', '<', Carbon::today());
    }

    /**
     * Scope para filtrar fichas que están por iniciar.
     */
    public function scopePorIniciar(Builder $query): Builder
    {
        return $query->where('fecha_inicio', '>', Carbon::today());
    }
}
