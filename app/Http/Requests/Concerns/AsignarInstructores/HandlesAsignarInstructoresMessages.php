<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

trait HandlesAsignarInstructoresMessages
{
    public function messages(): array
    {
        return [
            'instructores.required' => 'Debe seleccionar al menos un instructor.',
            'instructores.min' => 'Debe seleccionar al menos un instructor.',
            'instructores.max' => 'No se pueden asignar más de 10 instructores a una ficha.',
            'instructores.*.instructor_id.required' => 'Debe seleccionar un instructor.',
            'instructores.*.instructor_id.exists' => 'El instructor seleccionado no existe.',
            'instructores.*.fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'instructores.*.fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'instructores.*.fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'instructores.*.fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'instructores.*.fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'instructores.*.fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'instructores.*.total_horas_instructor.integer' => 'Las horas totales deben ser un número entero.',
            'instructores.*.total_horas_instructor.min' => 'Las horas totales deben ser al menos 1.',
            'instructores.*.total_horas_instructor.max' => 'Las horas totales no pueden exceder 1000.',
            'instructores.*.dias_semana.required' => 'Debe seleccionar al menos un día de formación.',
            'instructores.*.dias_semana.array' => 'Los días de formación deben ser una lista válida.',
            'instructores.*.dias_semana.min' => 'Debe seleccionar al menos un día de formación.',
            'instructores.*.dias_semana.max' => 'No se pueden asignar más de 7 días de formación.',
            'instructores.*.dias_semana.*.required' => 'El día seleccionado no es válido.',
            'instructores.*.dias_semana.*.integer' => 'El ID del día debe ser un número.',
            'instructores.*.dias_semana.*.exists' => 'El día seleccionado no existe en el sistema.',
            'instructores.*.dias.*.hora_inicio.required_with' => 'La hora de inicio es obligatoria cuando se selecciona un día.',
            'instructores.*.dias.*.hora_inicio.date_format' => 'El formato de la hora de inicio debe ser HH:MM.',
            'instructores.*.dias.*.hora_fin.required_with' => 'La hora de fin es obligatoria cuando se selecciona un día.',
            'instructores.*.dias.*.hora_fin.date_format' => 'El formato de la hora de fin debe ser HH:MM.',
            'instructores.*.dias.*.hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'instructores.*.dias_formacion.min' => 'Debe seleccionar al menos un día de formación.',
            'instructores.*.dias_formacion.max' => 'No se pueden asignar más de 7 días de formación.',
            'instructores.*.dias_formacion.*.dia_id.exists' => 'El día seleccionado no existe.',
            'instructor_principal_id.exists' => 'El instructor líder seleccionado no existe en el sistema.',
            'instructor_principal_id.integer' => 'El instructor líder debe ser un identificador válido.',
        ];
    }
}
