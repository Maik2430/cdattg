<?php

namespace App\Livewire\Programas\Concerns;

trait HandlesProgramaFormValidationHelpers
{
    protected function rules()
    {
        $rules = [
            'codigo' => 'required|string|max:6|regex:/^[0-9]+$/|unique:programas_formacion,codigo',
            'nombre' => 'required|string|max:255',
            'red_conocimiento_id' => 'required|exists:red_conocimientos,id',
            'nivel_formacion_id' => 'required|exists:parametros,id',
            'horas_totales' => 'required|integer|min:1|max:20000',
            'horas_etapa_lectiva' => 'required|integer|min:1|max:20000',
            'horas_etapa_productiva' => 'required|integer|min:1|max:20000',
        ];

        if ($this->isEdit && $this->programaId) {
            $rules['codigo'] = 'required|string|max:6|regex:/^[0-9]+$/|unique:programas_formacion,codigo,'.$this->programaId.',id';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'codigo.regex' => 'El código debe contener solo números (0-9).',
            'codigo.unique' => 'El código ya está siendo utilizado por otro programa.',
            'codigo.required' => 'El código es obligatorio.',
            'codigo.max' => 'El código no puede tener más de 6 caracteres.',
            'nombre.required' => 'El nombre del programa es obligatorio.',
            'red_conocimiento_id.required' => 'Debe seleccionar una red de conocimiento.',
            'nivel_formacion_id.required' => 'Debe seleccionar un nivel de formación.',
            'horas_totales.required' => 'Las horas totales son obligatorias.',
            'horas_totales.max' => 'Las horas totales no pueden superar las 20,000 horas.',
            'horas_etapa_lectiva.required' => 'Las horas lectivas son obligatorias.',
            'horas_etapa_lectiva.max' => 'Las horas lectivas no pueden superar las 20,000 horas.',
            'horas_etapa_productiva.required' => 'Las horas productivas son obligatorias.',
            'horas_etapa_productiva.max' => 'Las horas productivas no pueden superar las 20,000 horas.',
        ];
    }
}
