<?php

namespace App\Models\Concerns\Asistencia;

trait ManagesAsistenciaEstado
{
    /**
     * Verificar si la asistencia está activa
     */
    public function estaActiva(): bool
    {
        return ! $this->is_finished;
    }

    /**
     * Verificar si la asistencia está finalizada
     */
    public function estaFinalizada(): bool
    {
        return $this->is_finished;
    }

    /**
     * Finalizar la asistencia
     */
    public function finalizar(): bool
    {
        if ($this->is_finished) {
            return false;
        }

        $this->update([
            'is_finished' => true,
            'hora_fin' => now(),
            'user_edit_id' => auth()->id(),
        ]);

        return true;
    }
}
