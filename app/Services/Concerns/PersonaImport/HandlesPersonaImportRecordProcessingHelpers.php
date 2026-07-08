<?php

namespace App\Services\Concerns\PersonaImport;

use App\Models\PersonaImport;

trait HandlesPersonaImportRecordProcessingHelpers
{
    private function procesarRegistrosChunk(array $chunkRecords, array $existsCaches, PersonaImport $import): array
    {
        $processed = 0;
        $success = 0;
        $duplicates = 0;
        $missingContact = 0;

        foreach ($chunkRecords as $record) {
            $rowNumber = $record['row_number'];
            $data = $record['data'];
            $raw = $record['raw'];

            $processed++;

            $resultadoDuplicados = $this->validarDuplicados($data, $existsCaches, $import, $rowNumber, $raw);

            if ($resultadoDuplicados['es_duplicado']) {
                $duplicates++;

                continue;
            }

            $resultado = $this->procesarRegistro($data, $resultadoDuplicados, $import, $rowNumber);
            if ($resultado['success']) {
                $success++;
            } else {
                $duplicates++;
            }
            $missingContact += $resultado['missingContact'];
        }

        return [
            'processed' => $processed,
            'success' => $success,
            'duplicates' => $duplicates,
            'missingContact' => $missingContact,
        ];
    }

    private function procesarRegistro(
        array $data,
        array $resultadoDuplicados,
        PersonaImport $import,
        int $rowNumber
    ): array {
        $missingContact = 0;

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

            return ['success' => true, 'missingContact' => $missingContact];
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Integrity constraint violation')) {
                $resultado = $this->intentarReintento(
                    $e,
                    $data,
                    $resultadoDuplicados,
                    $import,
                    $rowNumber,
                    $missingContact
                );

                if ($resultado !== null) {
                    return $resultado;
                }
            }

            return $this->registrarErrorNoRecuperable($e, $data, $import, $rowNumber);
        }
    }
}
