<?php

namespace App\Http\Requests\Concerns\UpdateFichaCaracterizacion;

trait HandlesUpdateFichaCaracterizacionMessages
{
    public function messages(): array
    {
        return [
            'ficha.required' => 'El número de ficha es obligatorio.',
            'ficha.string' => 'El número de ficha debe ser texto.',
            'ficha.max' => 'El número de ficha no puede exceder 50 caracteres.',
            'ficha.unique' => 'Ya existe una ficha con este número.',
            'programa_formacion_id.required' => 'El programa de formación es obligatorio.',
            'programa_formacion_id.integer' => 'El programa de formación debe ser un número entero.',
            'programa_formacion_id.exists' => 'El programa de formación seleccionado no existe.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'instructor_id.integer' => 'El instructor debe ser un número entero.',
            'instructor_id.exists' => 'El instructor seleccionado no existe.',
            'ambiente_id.integer' => 'El ambiente debe ser un número entero.',
            'ambiente_id.exists' => 'El ambiente seleccionado no existe.',
            'modalidad_formacion_id.integer' => 'La modalidad de formación debe ser un número entero.',
            'modalidad_formacion_id.exists' => 'La modalidad de formación seleccionada no existe.',
            'sede_id.integer' => 'La sede debe ser un número entero.',
            'sede_id.exists' => 'La sede seleccionada no existe.',
            'jornada_id.integer' => 'La jornada debe ser un número entero.',
            'jornada_id.exists' => 'La jornada seleccionada no existe.',
            'total_horas.integer' => 'El total de horas debe ser un número entero.',
            'total_horas.min' => 'El total de horas debe ser al menos 1.',
            'total_horas.max' => 'El total de horas no puede exceder 9999.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
