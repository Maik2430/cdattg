<?php

namespace App\Models\Concerns\GuiasAprendizaje;

trait BuildsGuiasAprendizajeResumen
{
    /**
     * Obtener resumen de la guía.
     *
     * @return array<string, mixed>
     */
    public function obtenerResumen(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'estado' => $this->getEstadoFormateadoAttribute(),
            'resultados_count' => $this->contarResultadosAprendizaje(),
            'actividades_count' => $this->contarActividades(),
            'evidencias_count' => $this->contarEvidencias(),
            'porcentaje_completitud' => $this->porcentajeCompletitud(),
            'dias_desde_creacion' => $this->diasDesdeCreacion(),
            'puede_eliminarse' => $this->puedeSerEliminada(),
        ];
    }
}
