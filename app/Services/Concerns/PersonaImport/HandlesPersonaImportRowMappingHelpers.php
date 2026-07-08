<?php

namespace App\Services\Concerns\PersonaImport;

use App\Exceptions\ImportHeaderMismatchException;
use App\Services\PersonaImportNormalizer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait HandlesPersonaImportRowMappingHelpers
{
    private function mapearFila(array $row): array
    {
        $datos = [];

        foreach ($this->headerMap as $field => $column) {
            $valor = $row[$column] ?? null;
            $datos[$field] = is_string($valor) ? trim($valor) : $valor;
        }

        $valorTipoDocumentoOriginal = $datos['tipo_documento'] ?? null;
        if ($valorTipoDocumentoOriginal) {
            Log::debug('Valor tipo documento leído del Excel', [
                'valor_original' => $valorTipoDocumentoOriginal,
                'tipo' => gettype($valorTipoDocumentoOriginal),
            ]);
        }

        $datos['numero_documento'] = PersonaImportNormalizer::limpiarNumeroDocumento($datos['numero_documento'] ?? '');
        $datos['primer_nombre'] = $datos['primer_nombre'] ? Str::upper($datos['primer_nombre']) : null;
        $datos['segundo_nombre'] = $datos['segundo_nombre'] ? Str::upper($datos['segundo_nombre']) : null;
        $datos['primer_apellido'] = $datos['primer_apellido'] ? Str::upper($datos['primer_apellido']) : null;
        $datos['segundo_apellido'] = $datos['segundo_apellido'] ? Str::upper($datos['segundo_apellido']) : null;
        $datos['email'] = PersonaImportNormalizer::normalizarEmail($datos['email'] ?? null);
        $datos['celular'] = PersonaImportNormalizer::normalizarTelefono($datos['celular'] ?? null);
        $datos['telefono'] = PersonaImportNormalizer::normalizarTelefono($datos['telefono'] ?? null);
        $datos['tipo_documento_id'] = $this->resolverTipoDocumentoId($datos['tipo_documento'] ?? null);

        if (! $datos['tipo_documento_id'] && $valorTipoDocumentoOriginal) {
            $valorNormalizado = $valorTipoDocumentoOriginal
                ? Str::upper(Str::ascii(trim($valorTipoDocumentoOriginal)))
                : null;
            Log::warning('No se pudo resolver el tipo de documento', [
                'valor_original' => $valorTipoDocumentoOriginal,
                'valor_normalizado' => $valorNormalizado,
            ]);
        }

        return $datos;
    }

    private function filaVacia(array $row): bool
    {
        $valores = array_filter($row, fn ($value) => ! empty($value));

        return empty($valores);
    }

    private function leerChunk($reader, string $rutaArchivo, int $startRow, $filter): array
    {
        $filter->setRows($startRow, self::CHUNK_SIZE);
        $chunkSpreadsheet = $reader->load($rutaArchivo);
        $sheet = $chunkSpreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        $chunkRecords = [];
        $currentRowNumber = $startRow;

        foreach ($rows as $columns) {
            $rowNumber = $currentRowNumber++;

            if ($rowNumber === 1 && empty($this->headerMap)) {
                $this->headerMap = $this->resolverEncabezados($columns);

                if (empty($this->headerMap)) {
                    throw new ImportHeaderMismatchException;
                }

                continue;
            }

            $mapped = $this->mapearFila($columns);

            if ($this->filaVacia($mapped)) {
                continue;
            }

            $chunkRecords[] = [
                'row_number' => $rowNumber,
                'data' => $mapped,
                'raw' => $columns,
            ];
        }

        unset($sheet, $rows);
        $chunkSpreadsheet->disconnectWorksheets();
        unset($chunkSpreadsheet);

        return $chunkRecords;
    }
}
