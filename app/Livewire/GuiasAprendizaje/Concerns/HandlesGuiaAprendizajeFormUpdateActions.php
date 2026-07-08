<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

trait HandlesGuiaAprendizajeFormUpdateActions
{
    protected function updateGuiaAprendizaje(): string
    {
        $this->guia->update([
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'programa_formacion_id' => $this->programa_formacion_id,
            'duracion_horas' => $this->duracion_horas,
            'duracion_meses' => $this->duracion_meses,
            'objetivo_general' => $this->objetivo_general,
            'metodologia' => $this->metodologia,
            'evaluacion' => $this->evaluacion,
            'status' => $this->status,
            'user_edit_id' => auth()->id(),
        ]);

        $this->guia->resultadosAprendizaje()->sync($this->resultadosSeleccionados);

        return "Guía de aprendizaje '{$this->codigo}' actualizada correctamente";
    }
}
