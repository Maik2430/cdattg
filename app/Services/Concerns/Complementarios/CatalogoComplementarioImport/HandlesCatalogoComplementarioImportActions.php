<?php

namespace App\Services\Concerns\Complementarios\CatalogoComplementarioImport;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;
use Throwable;

trait HandlesCatalogoComplementarioImportActions
{
    public function importarCatalogo(UploadedFile $file): int
    {
        $rutaArchivo = $this->almacenarArchivoTemporal($file);

        try {
            return $this->procesarArchivo($rutaArchivo);
        } finally {
            Storage::disk('local')->delete($rutaArchivo);
        }
    }

    private function almacenarArchivoTemporal(UploadedFile $file): string
    {
        $timestamp = now()->format('Ymd_His');
        $extension = strtolower($file->getClientOriginalExtension() ?: 'xlsx');
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $safeBaseName = $baseName !== ''
            ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName)
            : 'catalogo';

        $fileName = "{$timestamp}_catalogo_{$safeBaseName}.{$extension}";

        return $file->storeAs('catalogo_complementarios', $fileName, 'local');
    }

    private function procesarArchivo(string $rutaRelativa): int
    {
        $rutaAbsoluta = Storage::disk('local')->path($rutaRelativa);

        if (! file_exists($rutaAbsoluta)) {
            throw new RuntimeException("No se encontró el archivo de catálogo en {$rutaAbsoluta}.");
        }

        $reader = IOFactory::createReaderForFile($rutaAbsoluta);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($rutaAbsoluta);
        $hoja = $spreadsheet->getActiveSheet();
        $rows = $hoja->toArray(null, true, true, true);

        unset($hoja);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        if (count($rows) <= 1) {
            return 0;
        }

        $encabezados = array_shift($rows);
        $mapaColumnas = $this->construirMapaColumnas($encabezados);

        $actualizados = 0;

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $datos = $this->mapearFila($row, $mapaColumnas);

                if ($datos === null) {
                    continue;
                }

                $actualizados += $this->upsertPrograma($datos);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Error importando catálogo de complementarios', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            throw $e;
        }

        return $actualizados;
    }
}
