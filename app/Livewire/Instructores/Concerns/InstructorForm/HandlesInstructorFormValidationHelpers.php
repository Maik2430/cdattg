<?php

namespace App\Livewire\Instructores\Concerns\InstructorForm;

trait HandlesInstructorFormValidationHelpers
{
    protected function rules(): array
    {
        return [
            'persona_id' => $this->isEdit ? 'required|exists:personas,id' : 'required|exists:personas,id',
            'regional_id' => 'required|exists:regionals,id',
            'centro_formacion_id' => 'nullable|exists:centro_formacions,id',
            'tipo_vinculacion_id' => 'nullable|exists:parametros_temas,id',
            'jornadas' => 'array',
            'jornadas.*' => 'exists:parametros_temas,id',
            'fecha_ingreso_sena' => 'nullable|date',
            'anos_experiencia' => 'nullable|integer|min:0|max:50',
            'experiencia_instructor_meses' => 'nullable|integer|min:0|max:600',
            'experiencia_laboral' => 'nullable|string|max:1000',
            'nivel_academico_id' => 'nullable|exists:parametros_temas,id',
            'formacion_pedagogia' => 'nullable|string|max:1000',
            'titulos_obtenidos.*' => 'nullable|string|max:200',
            'instituciones_educativas.*' => 'nullable|string|max:200',
            'certificaciones_tecnicas.*' => 'nullable|string|max:200',
            'cursos_complementarios.*' => 'nullable|string|max:200',
            'areas_experticia.*' => 'nullable|string|max:200',
            'competencias_tic.*' => 'nullable|string|max:200',
            'idiomas.*.idioma' => 'nullable|string|max:100',
            'idiomas.*.nivel' => 'nullable|string|max:50',
            'modalidades' => 'array',
            'modalidades.*' => 'exists:parametros_temas,id',
            'especialidades' => 'array',
            'numero_contrato' => 'nullable|string|max:50',
            'fecha_inicio_contrato' => 'nullable|date',
            'fecha_fin_contrato' => 'nullable|date|after_or_equal:fecha_inicio_contrato',
            'supervisor_contrato' => 'nullable|string|max:200',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'persona_id' => 'persona',
            'regional_id' => 'regional',
            'centro_formacion_id' => 'centro de formación',
            'tipo_vinculacion_id' => 'tipo de vinculación',
            'jornadas.*' => 'jornada',
            'nivel_academico_id' => 'nivel académico',
            'titulos_obtenidos.*' => 'título obtenido',
            'instituciones_educativas.*' => 'institución educativa',
            'certificaciones_tecnicas.*' => 'certificación técnica',
            'cursos_complementarios.*' => 'curso complementario',
            'areas_experticia.*' => 'área de experticia',
            'competencias_tic.*' => 'competencia TIC',
            'idiomas.*.idioma' => 'idioma',
            'idiomas.*.nivel' => 'nivel de idioma',
            'modalidades.*' => 'modalidad',
            'especialidades.principal' => 'especialidad principal',
            'especialidades.secundarias.*' => 'especialidad secundaria',
        ];
    }
}
