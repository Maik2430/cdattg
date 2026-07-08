<?php

namespace App\Livewire\Fichas\Concerns\FichaForm;

trait HandlesFichaFormValidationHelpers
{
    public function rules(): array
    {
        return [
            'ficha_codigo' => 'required|string|max:20|unique:fichas_caracterizacion,ficha,'.($this->ficha->id ?? 'null'),
            'programa_formacion_id' => 'required|exists:programas_formacion,id',
            'sede_id' => 'required|exists:sedes,id',
            'instructor_id' => 'required|exists:instructors,id',
            'ambiente_id' => 'required|exists:ambientes,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'modalidad_formacion_id' => 'required|exists:parametros,id',
            'jornada_id' => 'required|exists:parametros_temas,id',
            'total_horas' => 'nullable|integer|min:1|max:9999',
            'dias_formacion' => 'required|array|min:1',
            'dias_formacion.*' => 'exists:parametros,id',
            'status' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'ficha_codigo.required' => 'El código de la ficha es obligatorio',
            'ficha_codigo.unique' => 'Ya existe una ficha con este código',
            'programa_formacion_id.required' => 'Debe seleccionar un programa de formación',
            'programa_formacion_id.exists' => 'El programa de formación seleccionado no es válido',
            'sede_id.required' => 'Debe seleccionar una sede',
            'sede_id.exists' => 'La sede seleccionada no es válida',
            'instructor_id.required' => 'Debe seleccionar un instructor',
            'instructor_id.exists' => 'El instructor seleccionado no es válido',
            'ambiente_id.required' => 'Debe seleccionar un ambiente',
            'ambiente_id.exists' => 'El ambiente seleccionado no es válido',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'fecha_fin.required' => 'La fecha de fin es obligatoria',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio',
            'modalidad_formacion_id.required' => 'Debe seleccionar una modalidad de formación',
            'modalidad_formacion_id.exists' => 'La modalidad de formación seleccionada no es válida',
            'jornada_id.required' => 'Debe seleccionar una jornada de formación',
            'jornada_id.exists' => 'La jornada de formación seleccionada no es válida',
            'total_horas.required' => 'El total de horas es obligatorio',
            'total_horas.integer' => 'El total de horas debe ser un número entero',
            'total_horas.min' => 'El total de horas debe ser al menos 1',
            'total_horas.max' => 'El total de horas no puede exceder 9999',
            'dias_formacion.required' => 'Debe seleccionar al menos un día de formación',
            'dias_formacion.array' => 'Los días de formación deben ser un arreglo',
            'dias_formacion.min' => 'Debe seleccionar al menos un día de formación',
            'dias_formacion.*.exists' => 'El día de formación seleccionado no es válido',
            'status.required' => 'El estado es obligatorio',
            'status.boolean' => 'El estado debe ser verdadero o falso',
        ];
    }
}
