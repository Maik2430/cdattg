<?php

namespace App\Http\Requests\Concerns\CreateInstructor;

trait HandlesCreateInstructorMessages
{
    public function messages(): array
    {
        return [
            'persona_id.required' => 'Debe seleccionar una persona.',
            'persona_id.exists' => 'La persona seleccionada no existe o ya es instructor.',
            'regional_id.required' => 'Debe seleccionar una regional.',
            'regional_id.exists' => 'La regional seleccionada no existe.',
            'anos_experiencia.integer' => 'Los años de experiencia deben ser un número entero.',
            'anos_experiencia.min' => 'Los años de experiencia no pueden ser negativos.',
            'anos_experiencia.max' => 'Los años de experiencia no pueden ser mayores a 50.',
            'experiencia_laboral.max' => 'La experiencia laboral no puede exceder los 1000 caracteres.',
            'especialidades.array' => 'Las especialidades deben ser una lista válida.',
            'especialidades.*.exists' => 'Una o más especialidades seleccionadas no existen.',
            'tipo_vinculacion.in' => 'El tipo de vinculación debe ser: planta, contratista o apoyo a la formación.',
            'centro_formacion_id.exists' => 'El centro de formación seleccionado no existe.',
            'jornada_trabajo_id.exists' => 'La jornada de trabajo seleccionada no existe.',
            'experiencia_instructor_meses.integer' => 'La experiencia como instructor en meses debe ser un número entero.',
            'experiencia_instructor_meses.min' => 'La experiencia como instructor en meses no puede ser negativa.',
            'fecha_ingreso_sena.date' => 'La fecha de ingreso al SENA debe ser una fecha válida.',
            'programas_formacion.*.exists' => 'Uno o más programas de formación seleccionados no existen.',
            'nivel_academico_id.exists' => 'El nivel académico seleccionado no existe.',
            'formacion_pedagogia.max' => 'La formación en pedagogía no puede exceder los 1000 caracteres.',
            'idiomas.*.idioma.required_with' => 'Debe especificar el idioma.',
            'idiomas.*.nivel.required_with' => 'Debe especificar el nivel del idioma.',
            'idiomas.*.nivel.in' => 'El nivel del idioma debe ser: básico, intermedio, avanzado o nativo.',
            'habilidades_pedagogicas.*.in' => 'Las habilidades pedagógicas deben ser: virtual, presencial o dual.',
            'fecha_fin_contrato.after_or_equal' => 'La fecha de fin de contrato debe ser igual o posterior a la fecha de inicio.',
        ];
    }
}
