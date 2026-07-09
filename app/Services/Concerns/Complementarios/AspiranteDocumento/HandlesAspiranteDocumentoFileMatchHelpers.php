<?php

namespace App\Services\Concerns\Complementarios\AspiranteDocumento;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HandlesAspiranteDocumentoFileMatchHelpers
{
    private function buscarCoincidenciaEnArchivo(string $file, array $patrones, string $patronOriginal): bool
    {
        $fileName = basename($file);

        foreach ($patrones as $patronActual) {
            if (! $this->coincidePatronEnNombre($fileName, $patronActual)) {
                continue;
            }

            if ($this->verificarArchivoExiste($file, $fileName, $patronActual, $patronOriginal)) {
                return true;
            }
        }

        return false;
    }

    private function coincidePatronEnNombre(string $fileName, string $patron): bool
    {
        return strpos($fileName, $patron) !== false;
    }

    private function verificarArchivoExiste(string $file, string $fileName, string $patronActual, string $patronOriginal): bool
    {
        try {
            if (Storage::disk('google')->exists($file)) {
                Log::info('Documento encontrado en Google Drive', [
                    'archivo' => $fileName,
                    'patron_usado' => $patronActual,
                    'patron_original' => $patronOriginal,
                ]);

                return true;
            }
        } catch (Exception $e) {
            Log::warning("Error verificando existencia de archivo: {$fileName}", [
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    private function logDocumentoNoEncontrado(string $patron, array $patrones, int $totalArchivos): void
    {
        Log::warning('Documento no encontrado en Google Drive', [
            'patron' => $patron,
            'patrones_buscados' => $patrones,
            'total_archivos' => $totalArchivos,
        ]);
    }
}
