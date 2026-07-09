<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns\Complementarios\CreateAspirante;

use Illuminate\Validation\Rule;

trait HandlesCreateAspiranteRules
{
    public function rules(): array
    {
        return [
            'tipo_documento' => [
                'required_without:tipo_documento_id',
                'integer',
                Rule::exists('parametros', 'id'),
            ],
            'tipo_documento_id' => [
                'required_without:tipo_documento',
                'integer',
                Rule::exists('parametros', 'id'),
            ],
            'numero_documento' => [
                'required',
                'string',
                'max:191',
                Rule::unique('personas', 'numero_documento'),
            ],
            'primer_nombre' => [
                'required',
                'string',
                'max:191',
            ],
            'segundo_nombre' => [
                'nullable',
                'string',
                'max:191',
            ],
            'primer_apellido' => [
                'required',
                'string',
                'max:191',
            ],
            'segundo_apellido' => [
                'nullable',
                'string',
                'max:191',
            ],
            'fecha_nacimiento' => [
                'nullable',
                'date',
                'before:today',
            ],
            'genero_id' => [
                'nullable',
                'integer',
                Rule::exists('parametros', 'id'),
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:191',
            ],
            'celular' => [
                'nullable',
                'string',
                'max:191',
            ],
            'email' => [
                'nullable',
                'email',
                'max:191',
                Rule::unique('personas', 'email'),
            ],
            'pais_id' => [
                'nullable',
                'integer',
                Rule::exists('pais', 'id'),
            ],
            'departamento_id' => [
                'nullable',
                'integer',
                Rule::exists('departamentos', 'id'),
            ],
            'municipio_id' => [
                'nullable',
                'integer',
                Rule::exists('municipios', 'id'),
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:191',
            ],
            'caracterizaciones' => [
                'nullable',
                'array',
            ],
            'caracterizaciones.*' => [
                'integer',
                Rule::exists('parametros', 'id'),
            ],
            'nivel_escolaridad_id' => [
                'nullable',
                'integer',
                Rule::exists('parametros_temas', 'id'),
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:500',
            ],
            'documento_identidad' => [
                'required',
                'file',
                'mimes:pdf',
                'max:5120',
            ],
        ];
    }
}
