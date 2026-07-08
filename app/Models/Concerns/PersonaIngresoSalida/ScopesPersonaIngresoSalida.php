<?php

namespace App\Models\Concerns\PersonaIngresoSalida;

use Carbon\Carbon;

trait ScopesPersonaIngresoSalida
{
    /**
     * Scope para personas que están dentro actualmente
     */
    public function scopeDentro($query)
    {
        return $query->whereNull('timestamp_salida');
    }

    /**
     * Scope para filtrar por tipo de persona
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_persona', $tipo);
    }

    /**
     * Scope para filtrar por sede
     */
    public function scopePorSede($query, int $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }

    /**
     * Scope para filtrar por fecha
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha_entrada', $fecha);
    }

    /**
     * Scope para personas que entraron hoy
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_entrada', Carbon::today());
    }
}
