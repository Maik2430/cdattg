<?php

namespace App\Livewire\Concerns\CreateInstructor;

trait HandlesCreateInstructorValidationHelpers
{
    protected function rules(): array
    {
        return [
            'persona_id' => 'required|exists:personas,id',
            'regional_id' => 'required|exists:regionals,id',
            'centro_formacion_id' => 'nullable|exists:centro_formacions,id',
            'tipo_vinculacion_id' => 'nullable|exists:parametros_temas,id',
            'jornadas' => 'required|array|min:1',
            'jornadas.*' => 'required|exists:parametros_temas,id',
            'fecha_ingreso_sena' => 'nullable|date|before_or_equal:today',
            'anos_experiencia' => 'nullable|integer|min:0|max:50',
            'experiencia_instructor_meses' => 'nullable|integer|min:0',
            'experiencia_laboral' => 'nullable|string|max:1000',
            'nivel_academico_id' => 'nullable|exists:parametros_temas,id',
            'formacion_pedagogia' => 'nullable|string|max:500',
            'titulos_obtenidos' => 'nullable|array',
            'titulos_obtenidos.*' => 'nullable|string|max:255',
            'instituciones_educativas' => 'nullable|array',
            'instituciones_educativas.*' => 'nullable|string|max:255',
            'certificaciones_tecnicas' => 'nullable|array',
            'certificaciones_tecnicas.*' => 'nullable|string|max:255',
            'cursos_complementarios' => 'nullable|array',
            'cursos_complementarios.*' => 'nullable|string|max:255',
            'areas_experticia' => 'nullable|array',
            'areas_experticia.*' => 'nullable|string|max:255',
            'competencias_tic' => 'nullable|array',
            'competencias_tic.*' => 'nullable|string|max:255',
            'idiomas' => 'nullable|array',
            'idiomas.*.idioma' => 'nullable|string|max:100',
            'idiomas.*.nivel' => 'nullable|string|in:básico,intermedio,avanzado,nativo',
            'modalidades' => 'nullable|array',
            'modalidades.*' => 'nullable|exists:parametros_temas,id',
            'especialidades' => 'required|array|min:1',
            'especialidades.*' => 'required|exists:red_conocimientos,id',
            'numero_contrato' => 'nullable|string|max:100',
            'fecha_inicio_contrato' => 'nullable|date',
            'fecha_fin_contrato' => 'nullable|date|after_or_equal:fecha_inicio_contrato',
            'supervisor_contrato' => 'nullable|string|max:255',
            'eps' => 'nullable|string|max:100',
            'arl' => 'nullable|string|max:100',
        ];
    }

    protected function messages(): array
    {
        return [
            'persona_id.required' => 'La persona es obligatoria.',
            'persona_id.exists' => 'La persona seleccionada no existe.',
            'regional_id.required' => 'La regional es obligatoria.',
            'regional_id.exists' => 'La regional seleccionada no existe.',
            'jornadas.required' => 'Debe seleccionar al menos una jornada de trabajo.',
            'jornadas.array' => 'Las jornadas deben ser una lista.',
            'jornadas.min' => 'Debe seleccionar al menos una jornada de trabajo.',
            'jornadas.*.required' => 'Cada jornada seleccionada es obligatoria.',
            'jornadas.*.exists' => 'Una o más jornadas seleccionadas no existen en el sistema.',
            'especialidades.required' => 'Debe seleccionar al menos una especialidad (red de conocimiento).',
            'especialidades.array' => 'Las especialidades deben ser una lista.',
            'especialidades.min' => 'Debe seleccionar al menos una especialidad (red de conocimiento).',
            'especialidades.*.required' => 'Cada especialidad seleccionada es obligatoria.',
            'especialidades.*.exists' => 'Una o más especialidades seleccionadas no existen en el sistema.',
        ];
    }
}
