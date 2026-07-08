<?php

namespace App\Services\Concerns\PersonaImport;

use App\Models\PersonaImport;
use App\Models\PersonaImportIssue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaImportRetryErrorHelpers
{
    private function intentarReintento(
        \Throwable $e,
        array $data,
        array $resultadoDuplicados,
        PersonaImport $import,
        int $rowNumber,
        int &$missingContact
    ): ?array {
        $errorMessage = $e->getMessage();
        $camposDuplicados = $this->detectarCamposDuplicados($errorMessage, $data);

        if (empty($camposDuplicados)) {
            return null;
        }

        try {
            $faltantes = [
                'missing_email' => empty($data['email']),
                'missing_celular' => empty($data['celular']),
                'missing_telefono' => empty($data['telefono']),
            ];

            $this->persistirPersonaConUsuario(
                $resultadoDuplicados,
                $data,
                $import,
                $faltantes,
                $missingContact
            );

            $issueType = 'partial_import_'.implode('_', $camposDuplicados);
            $mensaje = 'Persona creada omitiendo campos duplicados: '
                .implode(', ', $camposDuplicados);

            PersonaImportIssue::create([
                'persona_import_id' => $import->id,
                'row_number' => $rowNumber,
                'issue_type' => $issueType,
                'numero_documento' => Arr::get($data, 'numero_documento'),
                'email' => Arr::get($data, 'email'),
                'celular' => Arr::get($data, 'celular'),
                'error_message' => $mensaje,
                'raw_payload' => $data,
            ]);

            return ['success' => true, 'missingContact' => $missingContact];
        } catch (\Throwable $retryError) {
            Log::error('Error en reintento de importación', [
                'import_id' => $import->id,
                'row' => $rowNumber,
                'error' => $retryError->getMessage(),
            ]);

            return null;
        }
    }

    private function detectarCamposDuplicados(string $errorMessage, array &$data): array
    {
        $camposDuplicados = [];

        if (str_contains($errorMessage, 'personas_email_unique') ||
            (str_contains($errorMessage, self::DUPLICATE_ENTRY_TEXT) &&
             str_contains($errorMessage, 'email'))) {
            $camposDuplicados[] = 'email';
            $data['email'] = null;
        }

        if (str_contains($errorMessage, 'personas_celular_unique') ||
            (str_contains($errorMessage, self::DUPLICATE_ENTRY_TEXT) &&
             str_contains($errorMessage, 'celular'))) {
            $camposDuplicados[] = 'celular';
            $data['celular'] = null;
        }

        if (str_contains($errorMessage, 'personas_telefono_unique') ||
            (str_contains($errorMessage, self::DUPLICATE_ENTRY_TEXT) &&
             str_contains($errorMessage, 'telefono'))) {
            $camposDuplicados[] = 'telefono';
            $data['telefono'] = null;
        }

        return $camposDuplicados;
    }

    private function registrarErrorNoRecuperable(
        \Throwable $e,
        array $data,
        PersonaImport $import,
        int $rowNumber
    ): array {
        Log::error('Error guardando persona en importación', [
            'import_id' => $import->id,
            'row' => $rowNumber,
            'error' => $e->getMessage(),
        ]);

        $issueType = $this->detectarTipoError($e);

        PersonaImportIssue::create([
            'persona_import_id' => $import->id,
            'row_number' => $rowNumber,
            'issue_type' => $issueType,
            'numero_documento' => Arr::get($data, 'numero_documento'),
            'email' => Arr::get($data, 'email'),
            'celular' => Arr::get($data, 'celular'),
            'error_message' => $e->getMessage(),
            'raw_payload' => $data,
        ]);

        return ['success' => false, 'missingContact' => 0];
    }

    private function detectarTipoError(\Throwable $e): string
    {
        $errorMessage = $e->getMessage();
        $tipoError = 'persist_error';

        if (str_contains($errorMessage, 'Integrity constraint violation')) {
            $patrones = [
                'duplicate_email_existing' => ['personas_email_unique', 'email'],
                'duplicate_document_existing' => ['personas_numero_documento_unique', 'numero_documento'],
                'duplicate_celular_existing' => ['personas_celular_unique', 'celular'],
                'duplicate_telefono_existing' => ['personas_telefono_unique', 'telefono'],
            ];

            foreach ($patrones as $tipo => $patronesBusqueda) {
                foreach ($patronesBusqueda as $patron) {
                    if (str_contains($errorMessage, $patron)) {
                        $tipoError = $tipo;
                        break 2;
                    }
                }
            }

            if ($tipoError === 'persist_error' && str_contains($errorMessage, self::DUPLICATE_ENTRY_TEXT)) {
                $tipoError = 'duplicate_generic';
            }
        }

        return $tipoError;
    }
}
