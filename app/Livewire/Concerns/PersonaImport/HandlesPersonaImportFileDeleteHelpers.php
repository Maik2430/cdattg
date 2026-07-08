<?php

namespace App\Livewire\Concerns\PersonaImport;

use App\Models\PersonaImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HandlesPersonaImportFileDeleteHelpers
{
    /**
     * Intenta eliminar un archivo con reintentos y liberación de recursos
     */
    private function eliminarArchivoConReintentos(string $disk, string $path, int $intentos = 3): void
    {
        gc_collect_cycles();

        for ($i = 0; $i < $intentos; $i++) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);

                    return;
                }

                return;
            } catch (\Throwable $e) {
                Log::warning('Intento de eliminación de archivo falló', [
                    'intento' => $i + 1,
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);

                if ($i < $intentos - 1) {
                    usleep(500000);
                    gc_collect_cycles();
                } else {
                    Log::warning('No se pudo eliminar el archivo después de '.$intentos.' intentos', [
                        'path' => $path,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    private function eliminarArchivoImportacionSiExiste(PersonaImport $importacion): void
    {
        if ($importacion->path && $importacion->disk) {
            $this->eliminarArchivoConReintentos($importacion->disk, $importacion->path);
        }
    }
}
