<?php

namespace App\Models\Concerns\GuiasAprendizaje;

trait ChecksGuiasAprendizajeEstado
{
    /**
     * Verificar si la guía está activa.
     */
    public function estaActiva(): bool
    {
        return $this->status == 1;
    }

    /**
     * Verificar si tiene actividades pendientes.
     */
    public function tieneActividadesPendientes(): bool
    {
        return $this->actividades()
            ->where('id_estado', '27')
            ->exists();
    }

    /**
     * Verificar si puede ser eliminada.
     */
    public function puedeSerEliminada(): bool
    {
        return $this->contarActividades() == 0;
    }
}
