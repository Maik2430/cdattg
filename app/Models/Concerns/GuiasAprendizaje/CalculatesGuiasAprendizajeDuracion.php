<?php

namespace App\Models\Concerns\GuiasAprendizaje;

use Carbon\Carbon;

trait CalculatesGuiasAprendizajeDuracion
{
    /**
     * Obtener días transcurridos desde creación.
     */
    public function diasDesdeCreacion(): int
    {
        if (! $this->created_at) {
            return 0;
        }

        return $this->created_at->diffInDays(Carbon::now());
    }

    /**
     * Obtener días transcurridos desde última actualización.
     */
    public function diasDesdeUltimaActualizacion(): int
    {
        if (! $this->updated_at) {
            return 0;
        }

        return $this->updated_at->diffInDays(Carbon::now());
    }
}
