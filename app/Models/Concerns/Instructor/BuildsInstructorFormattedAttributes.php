<?php

namespace App\Models\Concerns\Instructor;

trait BuildsInstructorFormattedAttributes
{
    /**
     * Obtener el estado formateado.
     */
    public function getEstadoFormateadoAttribute(): string
    {
        return $this->status ? 'ACTIVO' : 'INACTIVO';
    }

    /**
     * Obtener la fecha de creación formateada.
     */
    public function getFechaCreacionFormateadaAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : 'Sin fecha';
    }

    /**
     * Obtener la fecha de actualización formateada.
     */
    public function getFechaActualizacionFormateadaAttribute(): string
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : 'Sin fecha';
    }

    /**
     * Obtener años de experiencia formateados.
     */
    public function getAnosExperienciaFormateadosAttribute(): string
    {
        if (! $this->anos_experiencia) {
            return 'Sin especificar';
        }

        return $this->anos_experiencia.' año'.($this->anos_experiencia > 1 ? 's' : '');
    }
}
