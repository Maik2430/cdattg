<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns\Complementarios\CreateAspirante;

trait HandlesCreateAspiranteMessages
{
    public function messages(): array
    {
        return [
            'tipo_documento.required_without' => 'El tipo de documento es obligatorio.',
            'tipo_documento.exists' => 'El tipo de documento seleccionado no es válido.',
            'tipo_documento_id.required_without' => 'El tipo de documento es obligatorio.',
            'tipo_documento_id.exists' => 'El tipo de documento seleccionado no es válido.',
            'numero_documento.required' => 'El número de documento es obligatorio.',
            'numero_documento.unique' => 'Ya existe una persona registrada con este número de documento.',
            'numero_documento.max' => 'El número de documento no puede exceder los 191 caracteres.',
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.max' => 'El primer nombre no puede exceder los 191 caracteres.',
            'segundo_nombre.max' => 'El segundo nombre no puede exceder los 191 caracteres.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.max' => 'El primer apellido no puede exceder los 191 caracteres.',
            'segundo_apellido.max' => 'El segundo apellido no puede exceder los 191 caracteres.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a la fecha actual.',
            'genero_id.exists' => 'El género seleccionado no es válido.',
            'celular.max' => 'El número de celular no puede exceder los 191 caracteres.',
            'telefono.max' => 'El número de teléfono no puede exceder los 191 caracteres.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.unique' => 'Ya existe una persona registrada con este correo electrónico.',
            'email.max' => 'El correo electrónico no puede exceder los 191 caracteres.',
            'pais_id.exists' => 'El país seleccionado no es válido.',
            'departamento_id.exists' => 'El departamento seleccionado no es válido.',
            'municipio_id.exists' => 'El municipio seleccionado no es válido.',
            'direccion.max' => 'La dirección no puede exceder los 191 caracteres.',
            'caracterizaciones.array' => 'Las caracterizaciones deben ser una lista válida.',
            'caracterizaciones.*.exists' => 'Una o más caracterizaciones seleccionadas no existen.',
            'observaciones.max' => 'Las observaciones no pueden exceder los 500 caracteres.',
            'documento_identidad.required' => 'El documento de identidad es obligatorio.',
            'documento_identidad.file' => 'El documento de identidad debe ser un archivo válido.',
            'documento_identidad.mimes' => 'El documento de identidad debe ser un archivo PDF.',
            'documento_identidad.max' => 'El documento de identidad no puede ser mayor a 5MB.',
        ];
    }
}
