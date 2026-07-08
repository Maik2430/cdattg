<?php

namespace App\Models\Concerns\ResultadosAprendizaje;

trait BuildsResultadosAprendizajeAttributes
{
    /**
     * MÉTODO HELPER: Contar guías asociadas
     */
    public function contarGuiasAsociadas(): int
    {
        return $this->guiasAprendizaje()->count();
    }

    /**
     * MÉTODO HELPER: Obtener estado formateado
     */
    public function getEstadoFormateadoAttribute(): string
    {
        return $this->status == 1 ? 'ACTIVO' : 'INACTIVO';
    }

    /**
     * MÉTODO HELPER: Obtener nombre completo con código
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->codigo} - {$this->nombre}";
    }
}
