<?php

namespace App\Models\Concerns\Asistencia;

trait BuildsAsistenciaAttributes
{
    /**
     * Obtener la duración de la asistencia en formato legible
     */
    public function getDuracionAttribute(): string
    {
        if (! $this->hora_inicio) {
            return 'N/A';
        }

        $fin = $this->hora_fin ?? now();
        $duracion = $this->hora_inicio->diff($fin);

        return $duracion->format('%H:%I:%S');
    }

    /**
     * Obtener el número de aprendices registrados
     */
    public function getNumeroAprendicesAttribute(): int
    {
        return $this->asistenciaAprendices()->count();
    }
}
