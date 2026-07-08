<?php

namespace App\Models\Concerns\AsignacionInstructorLog;

trait BuildsAsignacionInstructorLogAttributes
{
    /**
     * Accessor para el nombre del instructor
     */
    public function getNombreInstructorAttribute(): string
    {
        return $this->instructor ? ($this->instructor->nombre_completo ?? 'Sin nombre') : 'Instructor eliminado';
    }

    /**
     * Accessor para el número de ficha
     */
    public function getNumeroFichaAttribute(): string
    {
        return $this->ficha ? ($this->ficha->ficha ?? 'Sin número') : 'Ficha eliminada';
    }

    /**
     * Accessor para el nombre del usuario
     */
    public function getNombreUsuarioAttribute(): string
    {
        return $this->user ? ($this->user->name ?? 'Sin nombre') : 'Usuario eliminado';
    }

    /**
     * Accessor para la fecha formateada
     */
    public function getFechaAccionFormateadaAttribute(): string
    {
        return $this->fecha_accion ? $this->fecha_accion->format('d/m/Y H:i:s') : 'Sin fecha';
    }
}
