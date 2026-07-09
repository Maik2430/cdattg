<?php

namespace App\Http\Controllers\Concerns\Persona;

use App\Models\Persona;

trait HandlesPersonaUpdateResponseHelpers
{
    /**
     * @return array<string, mixed>
     */
    protected function buildPersonaUpdateSuccessPayload(Persona $persona): array
    {
        $persona->loadMissing(['caracterizacionesComplementarias']);

        return [
            'success' => true,
            'message' => 'Información actualizada exitosamente',
            'data' => [
                'id' => $persona->id,
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
                'caracterizaciones' => $persona->caracterizacionesComplementarias->pluck('id')->toArray(),
            ],
        ];
    }
}
