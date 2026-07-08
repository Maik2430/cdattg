<?php

namespace App\Models\Concerns\FichaCaracterizacion;

use Carbon\Carbon;

trait HasFichaCaracterizacionEstadoTemporal
{
    /**
     * Verifica si la ficha está en curso actualmente.
     */
    public function estaEnCurso(): bool
    {
        if (! $this->fecha_inicio || ! $this->fecha_fin) {
            return false;
        }

        $hoy = Carbon::today();

        return $this->fecha_inicio <= $hoy && $this->fecha_fin >= $hoy;
    }

    /**
     * Verifica si la ficha ya terminó.
     */
    public function yaTermino(): bool
    {
        if (! $this->fecha_fin) {
            return false;
        }

        return $this->fecha_fin < Carbon::today();
    }

    /**
     * Verifica si la ficha está por iniciar.
     */
    public function estaPorIniciar(): bool
    {
        if (! $this->fecha_inicio) {
            return false;
        }

        return $this->fecha_inicio > Carbon::today();
    }

    /**
     * Calcula el porcentaje de avance de la ficha.
     */
    public function porcentajeAvance(): float
    {
        if (! $this->fecha_inicio || ! $this->fecha_fin) {
            return 0;
        }

        $hoy = Carbon::today();
        $duracionTotal = $this->fecha_inicio->diffInDays($this->fecha_fin);

        if ($duracionTotal === 0) {
            return 100;
        }

        $diasTranscurridos = $this->fecha_inicio->diffInDays($hoy);
        $porcentaje = ($diasTranscurridos / $duracionTotal) * 100;

        return min(100, max(0, round($porcentaje, 2)));
    }

    /**
     * Obtiene el estado textual de la ficha.
     */
    public function obtenerEstadoTexto(): string
    {
        if (! $this->status) {
            return 'Inactiva';
        }

        return match (true) {
            $this->estaPorIniciar() => 'Por iniciar',
            $this->estaEnCurso() => 'En curso',
            $this->yaTermino() => 'Terminada',
            default => 'Desconocido',
        };
    }
}
