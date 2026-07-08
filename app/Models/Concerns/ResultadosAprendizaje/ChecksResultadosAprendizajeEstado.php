<?php

namespace App\Models\Concerns\ResultadosAprendizaje;

trait ChecksResultadosAprendizajeEstado
{
    /**
     * MÉTODO HELPER: Verificar si el resultado está activo
     */
    public function isActivo(): bool
    {
        return $this->status == 1;
    }

    /**
     * MÉTODO HELPER: Verificar si está vigente
     * Siempre retorna true ya que no hay fechas de vigencia
     */
    public function estaVigente(): bool
    {
        return true;
    }
}
