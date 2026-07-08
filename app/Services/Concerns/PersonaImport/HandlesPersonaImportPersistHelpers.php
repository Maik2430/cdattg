<?php

namespace App\Services\Concerns\PersonaImport;

use App\Exceptions\MissingDocumentTypeException;
use App\Models\PersonaContactAlert;
use App\Models\PersonaImport;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait HandlesPersonaImportPersistHelpers
{
    private function persistirPersonaConUsuario(
        array $resultadoDuplicados,
        array $data,
        PersonaImport $import,
        array $faltantes,
        int &$missingContact
    ): void {
        DB::transaction(function () use ($resultadoDuplicados, $data, $import, $faltantes, &$missingContact) {
            $tipoDocumentoId = $resultadoDuplicados['tipo_documento_id'] ?? null;
            if (! $tipoDocumentoId) {
                throw new MissingDocumentTypeException($data['tipo_documento'] ?? null);
            }

            $datosPersona = [
                'tipo_documento' => $tipoDocumentoId,
                'numero_documento' => $data['numero_documento'],
                'primer_nombre' => $data['primer_nombre'],
                'segundo_nombre' => Arr::get($data, 'segundo_nombre'),
                'primer_apellido' => Arr::get($data, 'primer_apellido'),
                'segundo_apellido' => Arr::get($data, 'segundo_apellido'),
                'telefono' => Arr::get($data, 'telefono'),
                'celular' => Arr::get($data, 'celular'),
                'email' => Arr::get($data, 'email'),
                'status' => 1,
            ];

            $persona = $this->personaService->crearSinUsuario($datosPersona, $import->user_id);

            if (! in_array(true, $faltantes, true)) {
                return;
            }

            PersonaContactAlert::create([
                'persona_id' => $persona->id,
                'persona_import_id' => $import->id,
                'missing_email' => $faltantes['missing_email'],
                'missing_celular' => $faltantes['missing_celular'],
                'missing_telefono' => $faltantes['missing_telefono'],
                'raw_payload' => $data,
            ]);

            $missingContact++;
        });
    }
}
