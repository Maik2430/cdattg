<?php

namespace App\Services\Concerns\PersonaImport;

use App\Exceptions\ImportFileNotFoundException;
use App\Models\PersonaImport;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

trait HandlesPersonaImportProcesarActions
{
    public function procesar(PersonaImport $import): void
    {
        $this->warmDocumentoCache();

        $import->update([
            'status' => 'processing',
            'processed_rows' => 0,
            'success_count' => 0,
            'duplicate_count' => 0,
            'missing_contact_count' => 0,
        ]);

        $rutaArchivo = Storage::disk($import->disk)->path($import->path);

        if (! file_exists($rutaArchivo)) {
            throw new ImportFileNotFoundException($rutaArchivo);
        }

        $reader = IOFactory::createReaderForFile($rutaArchivo);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($rutaArchivo);
        $hoja = $spreadsheet->getActiveSheet();
        $highestRow = $hoja->getHighestDataRow();
        unset($hoja);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        if ($highestRow <= 1) {
            $import->update([
                'total_rows' => 0,
                'status' => 'completed',
            ]);

            unset($reader);
            gc_collect_cycles();
        } else {
            $filter = new class implements IReadFilter
            {
                private int $startRow = 1;

                private int $endRow = 1;

                public function setRows(int $startRow, int $chunkSize): void
                {
                    $this->startRow = $startRow;
                    $this->endRow = $startRow + $chunkSize - 1;
                }

                public function readCell($columnAddress, $row, $worksheetName = ''): bool
                {
                    return $row >= $this->startRow && $row <= $this->endRow;
                }
            };

            $reader->setReadFilter($filter);

            $import->update([
                'total_rows' => $highestRow - 1,
            ]);

            $totalRowsValidas = $this->procesarChunks($reader, $rutaArchivo, $highestRow, $filter, $import);

            $import->update([
                'total_rows' => $totalRowsValidas,
            ]);

            $import->update([
                'status' => 'completed',
            ]);
        }

        unset($reader, $filter);
        gc_collect_cycles();

        if (PHP_OS_FAMILY === 'Windows') {
            usleep(250000);
            gc_collect_cycles();
        }
    }

    private function procesarChunks(
        $reader,
        string $rutaArchivo,
        int $highestRow,
        $filter,
        PersonaImport $import
    ): int {
        $processed = 0;
        $success = 0;
        $duplicates = 0;
        $missingContact = 0;

        for ($startRow = 1; $startRow <= $highestRow; $startRow += self::CHUNK_SIZE) {
            $chunkRecords = $this->leerChunk($reader, $rutaArchivo, $startRow, $filter);

            if (empty($chunkRecords)) {
                continue;
            }

            $existsCaches = $this->consultarExistencias($chunkRecords);
            $resultados = $this->procesarRegistrosChunk($chunkRecords, $existsCaches, $import);

            $processed += $resultados['processed'];
            $success += $resultados['success'];
            $duplicates += $resultados['duplicates'];
            $missingContact += $resultados['missingContact'];

            $import->update([
                'processed_rows' => $processed,
                'success_count' => $success,
                'duplicate_count' => $duplicates,
                'missing_contact_count' => $missingContact,
            ]);
        }

        return $processed;
    }
}
