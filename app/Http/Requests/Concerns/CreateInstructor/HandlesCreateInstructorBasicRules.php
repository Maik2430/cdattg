<?php

namespace App\Http\Requests\Concerns\CreateInstructor;

trait HandlesCreateInstructorBasicRules
{
    protected function createInstructorBasicRules(): array
    {
        return [
            'persona_id' => [
                'required',
                'integer',
                'exists:personas,id',
            ],
            'regional_id' => [
                'required',
                'integer',
                'exists:regionals,id',
            ],
            'anos_experiencia' => [
                'nullable',
                'integer',
                'min:0',
                'max:50',
            ],
            'experiencia_laboral' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'especialidades' => [
                'nullable',
                'array',
            ],
            'especialidades.*' => [
                'integer',
                'exists:red_conocimientos,id',
            ],
            'tipo_vinculacion_id' => [
                'nullable',
                'integer',
                'exists:parametros_temas,id',
            ],
            'centro_formacion_id' => [
                'nullable',
                'integer',
                'exists:centro_formacions,id',
            ],
            'jornadas' => [
                'nullable',
                'array',
            ],
            'jornadas.*' => [
                'integer',
                'exists:parametros_temas,id',
            ],
            'experiencia_instructor_meses' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'fecha_ingreso_sena' => [
                'nullable',
                'date',
            ],
        ];
    }
}
