<?php

namespace App\Livewire\Aprendices\Concerns;

trait HandlesAprendizFormValidationHelpers
{
    protected function rules()
    {
        $rules = [
            'persona_id' => 'required|exists:personas,id',
            'ficha_caracterizacion_id' => 'required|exists:fichas_caracterizacion,id',
            'estado' => 'required|boolean',
        ];

        if ($this->isEdit) {
            unset($rules['persona_id']);
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'persona_id.required' => 'La persona es obligatoria.',
            'persona_id.exists' => 'La persona seleccionada no es válida.',
            'ficha_caracterizacion_id.required' => 'La ficha es obligatoria.',
            'ficha_caracterizacion_id.exists' => 'La ficha seleccionada no es válida.',
            'estado.required' => 'El estado es obligatorio.',
        ];
    }
}
