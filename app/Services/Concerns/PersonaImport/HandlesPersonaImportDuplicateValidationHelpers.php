<?php

namespace App\Services\Concerns\PersonaImport;

use App\Models\Persona;
use App\Models\PersonaImport;
use App\Models\PersonaImportIssue;

trait HandlesPersonaImportDuplicateValidationHelpers
{
    private function consultarExistencias(array $records): array
    {
        $documentos = array_values(
            array_unique(
                array_filter(
                    array_map(fn ($item) => $item['data']['numero_documento'] ?? null, $records)
                )
            )
        );

        if (empty($documentos)) {
            return ['documentos' => []];
        }

        $existentes = Persona::whereIn('numero_documento', $documentos)
            ->pluck('numero_documento')
            ->map(fn ($doc) => (string) $doc)
            ->toArray();

        return [
            'documentos' => $existentes,
        ];
    }

    private function validarDuplicados(
        array $data,
        array $existsCaches,
        PersonaImport $import,
        int $rowNumber,
        array $raw
    ): array {
        $esDuplicado = false;
        $tipoDocumentoId = $data['tipo_documento_id'] ?? null;
        $numeroDocumento = $data['numero_documento'] ?? null;
        $email = $data['email'] ?? null;
        $celular = $data['celular'] ?? null;
        $issueType = null;

        if (! $tipoDocumentoId) {
            $esDuplicado = true;
            $issueType = 'missing_document_type';
            $tipoDocumentoId = null;
        } elseif (! $numeroDocumento) {
            $esDuplicado = true;
            $issueType = 'missing_document';
        } elseif (empty($data['primer_nombre']) || empty($data['primer_apellido'])) {
            $esDuplicado = true;
            $issueType = 'missing_required_fields';
        } elseif (isset($this->documentSeen[$numeroDocumento])) {
            $esDuplicado = true;
            $issueType = 'duplicate_document_in_file';
        } elseif (in_array($numeroDocumento, $existsCaches['documentos'], true)) {
            $esDuplicado = true;
            $issueType = 'duplicate_document_existing';
        } else {
            $this->documentSeen[$numeroDocumento] = true;
        }

        if ($issueType) {
            $this->registrarIssue(
                $import,
                $rowNumber,
                $issueType,
                $numeroDocumento,
                $email,
                $celular,
                $raw
            );
        }

        return [
            'es_duplicado' => $esDuplicado,
            'tipo_documento_id' => $tipoDocumentoId,
        ];
    }

    private function registrarIssue(
        PersonaImport $import,
        int $rowNumber,
        string $issueType,
        ?string $documento,
        ?string $email,
        ?string $celular,
        array $raw
    ): void {
        PersonaImportIssue::create([
            'persona_import_id' => $import->id,
            'row_number' => $rowNumber,
            'issue_type' => $issueType,
            'numero_documento' => $documento,
            'email' => $email,
            'celular' => $celular,
            'raw_payload' => $raw,
        ]);
    }
}
