<?php

namespace App\Http\Requests\Concerns\StoreInstructor;

trait HandlesStoreInstructorAttributes
{
    public function attributes(): array
    {
        return [
            'tipo_documento' => 'tipo de documento',
            'numero_documento' => 'número de documento',
            'primer_nombre' => 'primer nombre',
            'segundo_nombre' => 'segundo nombre',
            'primer_apellido' => 'primer apellido',
            'segundo_apellido' => 'segundo apellido',
            'fecha_de_nacimiento' => 'fecha de nacimiento',
            'genero' => 'género',
            'email' => 'correo electrónico',
            'regional_id' => 'regional',
            'telefono' => 'teléfono',
            'celular' => 'celular',
            'direccion' => 'dirección',
        ];
    }
}
