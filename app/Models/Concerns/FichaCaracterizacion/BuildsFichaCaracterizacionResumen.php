<?php

namespace App\Models\Concerns\FichaCaracterizacion;

trait BuildsFichaCaracterizacionResumen
{
    /**
     * Obtiene información resumida de la ficha.
     *
     * @return array<string, mixed>
     */
    public function obtenerResumen(): array
    {
        return [
            'id' => $this->id,
            'numero_ficha' => $this->ficha,
            'programa' => $this->programaFormacion->nombre ?? 'N/A',
            'instructor' => $this->instructor ?
                $this->instructor->persona->primer_nombre.' '.$this->instructor->persona->primer_apellido : 'N/A',
            'sede' => $this->sede->nombre ?? 'N/A',
            'modalidad' => $this->modalidadFormacion->name ?? 'N/A',
            'fecha_inicio' => $this->fecha_inicio?->format('d/m/Y'),
            'fecha_fin' => $this->fecha_fin?->format('d/m/Y'),
            'duracion_dias' => $this->duracionEnDias(),
            'total_horas' => $this->total_horas ?? 0,
            'aprendices_count' => $this->contarAprendices(),
            'estado' => $this->obtenerEstadoTexto(),
            'porcentaje_avance' => $this->porcentajeAvance(),
        ];
    }
}
