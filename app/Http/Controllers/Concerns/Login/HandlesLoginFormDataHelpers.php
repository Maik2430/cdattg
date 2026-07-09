<?php

namespace App\Http\Controllers\Concerns\Login;

trait HandlesLoginFormDataHelpers
{
    /**
     * Obtener datos del usuario para pre-llenar el formulario
     */
    private function getUserDataForForm($user)
    {
        $persona = $user->persona;

        if (! $persona) {
            return [];
        }

        return [
            'tipo_documento' => $persona->tipo_documento,
            'numero_documento' => $persona->numero_documento,
            'primer_nombre' => $persona->primer_nombre,
            'segundo_nombre' => $persona->segundo_nombre,
            'primer_apellido' => $persona->primer_apellido,
            'segundo_apellido' => $persona->segundo_apellido,
            'fecha_nacimiento' => $persona->fecha_nacimiento,
            'genero' => $persona->genero,
            'telefono' => $persona->telefono,
            'celular' => $persona->celular,
            'email' => $persona->email,
            'pais_id' => $persona->pais_id,
            'departamento_id' => $persona->departamento_id,
            'municipio_id' => $persona->municipio_id,
            'direccion' => $persona->direccion,
        ];
    }
}
