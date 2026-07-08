<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\GuiasAprendizaje;

trait HandlesGuiaAprendizajeFormStoreActions
{
    protected function storeGuiaAprendizaje(): string
    {
        $guia = GuiasAprendizaje::create([
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
            'user_create_id' => auth()->id(),
        ]);

        if (! empty($this->resultadosSeleccionados)) {
            $guia->resultadosAprendizaje()->attach($this->resultadosSeleccionados);
        }

        return "Guía de aprendizaje '{$this->codigo}' creada correctamente";
    }
}
