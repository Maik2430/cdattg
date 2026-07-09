<?php

namespace App\Http\Requests\Concerns\CreateInstructor;

trait HandlesCreateInstructorExtendedRules
{
    protected function createInstructorExtendedRules(): array
    {
        return [
            'nivel_academico_id' => [
                'nullable',
                'integer',
                'exists:parametros,id',
            ],
            'titulos_obtenidos' => [
                'nullable',
                'array',
            ],
            'titulos_obtenidos.*' => [
                'string',
                'max:255',
            ],
            'instituciones_educativas' => [
                'nullable',
                'array',
            ],
            'instituciones_educativas.*' => [
                'string',
                'max:255',
            ],
            'certificaciones_tecnicas' => [
                'nullable',
                'array',
            ],
            'certificaciones_tecnicas.*' => [
                'string',
                'max:255',
            ],
            'cursos_complementarios' => [
                'nullable',
                'array',
            ],
            'cursos_complementarios.*' => [
                'string',
                'max:255',
            ],
            'formacion_pedagogia' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'areas_experticia' => [
                'nullable',
            ],
            'competencias_tic' => [
                'nullable',
            ],
            'idiomas' => [
                'nullable',
                'array',
            ],
            'idiomas.*.idioma' => [
                'required_with:idiomas',
                'string',
                'max:100',
            ],
            'idiomas.*.nivel' => [
                'required_with:idiomas',
                'string',
                'in:básico,intermedio,avanzado,nativo',
            ],
            'habilidades_pedagogicas' => [
                'nullable',
                'array',
            ],
            'habilidades_pedagogicas.*' => [
                'string',
                'in:virtual,presencial,dual',
            ],
            'documentos_adjuntos' => [
                'nullable',
                'array',
            ],
            'numero_contrato' => [
                'nullable',
                'string',
                'max:100',
            ],
            'fecha_inicio_contrato' => [
                'nullable',
                'date',
            ],
            'fecha_fin_contrato' => [
                'nullable',
                'date',
                'after_or_equal:fecha_inicio_contrato',
            ],
            'supervisor_contrato' => [
                'nullable',
                'string',
                'max:255',
            ],
            'eps' => [
                'nullable',
                'string',
                'max:255',
            ],
            'arl' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
