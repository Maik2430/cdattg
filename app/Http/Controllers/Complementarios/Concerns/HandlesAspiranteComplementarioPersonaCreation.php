<?php

namespace App\Http\Controllers\Complementarios\Concerns;

use App\Models\ParametroTema;
use App\Models\Persona;

trait HandlesAspiranteComplementarioPersonaCreation
{
    /** @param array<string, mixed> $validated */
    private function crearPersonaDesdeValidacion(array $validated): Persona
    {
        $tipoDocumentoId = $validated['tipo_documento_id'] ?? $validated['tipo_documento'] ?? null;

        $datosPersona = [
            'tipo_documento' => $tipoDocumentoId,
            'numero_documento' => $validated['numero_documento'],
            'primer_nombre' => $validated['primer_nombre'],
            'segundo_nombre' => $validated['segundo_nombre'] ?? null,
            'primer_apellido' => $validated['primer_apellido'],
            'segundo_apellido' => $validated['segundo_apellido'] ?? null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
            'genero' => $validated['genero_id'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'celular' => $validated['celular'] ?? null,
            'email' => $validated['email'] ?? null,
            'pais_id' => $validated['pais_id'] ?? null,
            'departamento_id' => $validated['departamento_id'] ?? null,
            'municipio_id' => $validated['municipio_id'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
            'caracterizacion_ids' => $validated['caracterizaciones'] ?? [],
            'nivel_escolaridad_id' => $validated['nivel_escolaridad_id'] ?? null,
        ];

        return $this->personaService->crear(
            $this->convertirParametrosAParametrosTemas($datosPersona)
        );
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function convertirParametrosAParametrosTemas(array $data): array
    {
        $conversiones = [
            'tipo_documento' => 2,
            'genero' => 3,
            'nivel_escolaridad_id' => 23,
        ];

        foreach ($conversiones as $campo => $temaId) {
            if (! isset($data[$campo])) {
                continue;
            }

            $parametroTema = ParametroTema::where('tema_id', $temaId)
                ->where('parametro_id', $data[$campo])
                ->first();

            if ($parametroTema) {
                $data[$campo] = $parametroTema->id;
            }
        }

        return $data;
    }
}
