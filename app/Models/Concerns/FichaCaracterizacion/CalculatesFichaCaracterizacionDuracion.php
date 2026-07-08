<?php

namespace App\Models\Concerns\FichaCaracterizacion;

trait CalculatesFichaCaracterizacionDuracion
{
    /**
     * Calcula la duración de la ficha en días.
     */
    public function duracionEnDias(): int
    {
        if (! $this->fecha_inicio || ! $this->fecha_fin) {
            return 0;
        }

        return $this->fecha_inicio->diffInDays($this->fecha_fin) + 1;
    }

    /**
     * Calcula la duración de la ficha en meses.
     */
    public function duracionEnMeses(): int
    {
        if (! $this->fecha_inicio || ! $this->fecha_fin) {
            return 0;
        }

        return $this->fecha_inicio->diffInMonths($this->fecha_fin);
    }

    /**
     * Calcula las horas promedio por día.
     */
    public function horasPromedioPorDia(): float
    {
        $duracionDias = $this->duracionEnDias();

        if ($duracionDias === 0 || ! $this->total_horas) {
            return 0;
        }

        return round($this->total_horas / $duracionDias, 2);
    }
}
