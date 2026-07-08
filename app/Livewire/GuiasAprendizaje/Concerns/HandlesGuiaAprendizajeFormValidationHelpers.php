<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

trait HandlesGuiaAprendizajeFormValidationHelpers
{
    protected function guiaAprendizajeFormValidationRules(): array
    {
        return [
            'codigo' => 'required|string|max:20|unique:guia_aprendizajes,codigo'
                .($this->isEdit ? ','.$this->guia->id : ''),
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'programa_formacion_id' => 'required|exists:programas_formacion,id',
            'duracion_horas' => 'required|integer|min:1|max:999',
            'duracion_meses' => 'required|integer|min:1|max:12',
            'objetivo_general' => 'nullable|string|max:500',
            'metodologia' => 'nullable|string|max:1000',
            'evaluacion' => 'nullable|string|max:1000',
            'status' => 'boolean',
        ];
    }
}
