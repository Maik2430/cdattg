<?php

namespace App\Models\Concerns\GuiasAprendizaje;

trait BuildsGuiasAprendizajeFormattedAttributes
{
    /**
     * Formatear código para mostrar.
     */
    public function getCodigoFormateadoAttribute(): string
    {
        return strtoupper($this->codigo);
    }

    /**
     * Formatear nombre para mostrar.
     */
    public function getNombreFormateadoAttribute(): string
    {
        return strtoupper($this->nombre);
    }

    /**
     * Obtener estado formateado.
     */
    public function getEstadoFormateadoAttribute(): string
    {
        return $this->status == 1 ? 'ACTIVO' : 'INACTIVO';
    }
}
