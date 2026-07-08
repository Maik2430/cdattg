<?php

namespace App\Models\Concerns\PersonaIngresoSalida;

use Carbon\Carbon;

trait ManagesPersonaIngresoSalidaPresencia
{
    /**
     * Verifica si la persona está dentro (tiene entrada sin salida)
     */
    public function estaDentro(): bool
    {
        return is_null($this->timestamp_salida);
    }

    /**
     * Calcula el tiempo que la persona ha estado dentro
     * Retorna null si aún no ha salido
     */
    public function tiempoDentro(): ?int
    {
        if ($this->estaDentro()) {
            return Carbon::now()->diffInMinutes($this->timestamp_entrada);
        }

        if ($this->timestamp_salida && $this->timestamp_entrada) {
            return $this->timestamp_salida->diffInMinutes($this->timestamp_entrada);
        }

        return null;
    }
}
