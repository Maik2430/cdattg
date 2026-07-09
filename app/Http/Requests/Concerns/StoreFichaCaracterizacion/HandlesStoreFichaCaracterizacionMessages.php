<?php

namespace App\Http\Requests\Concerns\StoreFichaCaracterizacion;

trait HandlesStoreFichaCaracterizacionMessages
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
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hace 2 años.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'instructor_id.required' => 'El instructor principal es obligatorio.',
            'instructor_id.integer' => 'El instructor debe ser un número entero.',
            'instructor_id.exists' => 'El instructor seleccionado no existe.',
            'ambiente_id.required' => 'El ambiente es obligatorio.',
            'ambiente_id.integer' => 'El ambiente debe ser un número entero.',
            'ambiente_id.exists' => 'El ambiente seleccionado no existe.',
            'modalidad_formacion_id.required' => 'La modalidad de formación es obligatoria.',
            'modalidad_formacion_id.integer' => 'La modalidad de formación debe ser un número entero.',
            'modalidad_formacion_id.exists' => 'La modalidad de formación seleccionada no existe.',
            'sede_id.required' => 'La sede es obligatoria.',
            'sede_id.integer' => 'La sede debe ser un número entero.',
            'sede_id.exists' => 'La sede seleccionada no existe.',
            'jornada_id.required' => 'La jornada de formación es obligatoria.',
            'jornada_id.integer' => 'La jornada debe ser un número entero.',
            'jornada_id.exists' => 'La jornada seleccionada no existe.',
            'total_horas.required' => 'El total de horas es obligatorio.',
            'total_horas.integer' => 'El total de horas debe ser un número entero.',
            'total_horas.min' => 'El total de horas debe ser al menos 1.',
            'total_horas.max' => 'El total de horas no puede exceder 9999.',
            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
            'dias_formacion.required' => 'Debe seleccionar al menos un día de formación.',
            'dias_formacion.array' => 'Los días de formación deben ser una lista.',
            'dias_formacion.min' => 'Debe seleccionar al menos un día de formación.',
            'dias_formacion.*.required' => 'Cada día de formación es obligatorio.',
            'dias_formacion.*.integer' => 'El día de formación debe ser un número entero.',
            'dias_formacion.*.in' => 'El día de formación seleccionado no es válido.',
            'horarios.array' => 'Los horarios deben ser una lista.',
            'horarios.*.array' => 'Cada horario debe ser una lista.',
            'horarios.*.hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
            'horarios.*.hora_fin.date_format' => 'La hora de fin debe tener el formato HH:MM.',
            'horarios.*.hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}
