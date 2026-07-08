<?php

namespace App\Models\Concerns\GuiasAprendizaje;

trait CountsGuiasAprendizajeAsociaciones
{
    /**
     * Contar resultados de aprendizaje asociados.
     */
    public function contarResultadosAprendizaje(): int
    {
        return $this->resultadosAprendizaje()->count();
    }

    /**
     * Contar actividades asociadas.
     */
    public function contarActividades(): int
    {
        return $this->actividades()->count();
    }

    /**
     * Contar evidencias asociadas.
     */
    public function contarEvidencias(): int
    {
        return $this->evidencias()->count();
    }

    /**
     * Obtener porcentaje de completitud de actividades.
     */
    public function porcentajeCompletitud(): float
    {
        $totalActividades = $this->contarActividades();

        if ($totalActividades == 0) {
            return 0.0;
        }

        $actividadesCompletadas = $this->actividades()
            ->where('id_estado', '25')
            ->count();

        return round(($actividadesCompletadas / $totalActividades) * 100, 2);
    }
}
