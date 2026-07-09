<?php

namespace App\Http\Requests\Concerns\StoreInstructor;

trait HandlesStoreInstructorRules
{
    public function rules(): array
    {
        return [
            'tipo_documento' => [
                'required',
                'integer',
                'exists:parametros,id',
            ],
            'numero_documento' => [
                'required',
                'string',
                'max:20',
                'unique:personas,numero_documento',
            ],
            'primer_nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            ],
            'segundo_nombre' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            ],
            'primer_apellido' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            ],
            'segundo_apellido' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            ],
            'fecha_de_nacimiento' => [
                'required',
                'date',
                'before:today',
                'after:1900-01-01',
            ],
            'genero' => [
                'required',
                'integer',
                'exists:parametros,id',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:personas,email',
                'unique:users,email',
            ],
            'regional_id' => [
                'required',
                'integer',
                'exists:regionales,id',
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
                'unique:personas,telefono',
            ],
            'celular' => [
                'nullable',
                'string',
                'max:20',
                'unique:personas,celular',
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }
}
