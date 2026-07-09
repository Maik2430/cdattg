<?php

namespace App\Services\Concerns\Complementarios\AspiranteDocumento;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HandlesAspiranteDocumentoSearchActions
{
    public function buscarDocumentoEnGoogleDrive(array $files, string $patron): bool
    {
        $patrones = $this->generarVariantesPatron($patron);

        foreach ($files as $file) {
            if ($this->buscarCoincidenciaEnArchivo($file, $patrones, $patron)) {
                return true;
            }
        }

        $this->logDocumentoNoEncontrado($patron, $patrones, count($files));

        return false;
    }

    public function encontrarArchivoEnGoogleDrive(string $patron): ?string
    {
        $files = Storage::disk('google')->files('documentos_aspirantes');

        $patrones = [$patron];

        if (strpos($patron, '_') !== false) {
            $patronConEspacios = str_replace('_', ' ', $patron);
            $patrones[] = $patronConEspacios;
        }

        if (strpos($patron, ' ') !== false) {
            $patronConGuiones = str_replace(' ', '_', $patron);
            $patrones[] = $patronConGuiones;
        }

        $patronSinNombres = $this->crearPatronSinNombres($patron);
        if ($patronSinNombres) {
            $patrones[] = $patronSinNombres;

            $patronSinNombresConEspacios = str_replace('_', ' ', $patronSinNombres);
            $patrones[] = $patronSinNombresConEspacios;
        }

        foreach ($files as $file) {
            $fileName = basename($file);

            foreach ($patrones as $patronActual) {
                if (strpos($fileName, $patronActual) !== false) {
                    Log::info('Archivo encontrado para descarga', [
                        'archivo' => $fileName,
                        'patron_usado' => $patronActual,
                        'patron_original' => $patron,
                    ]);

                    return $file;
                }
            }
        }

        Log::warning('Archivo no encontrado para descarga', [
            'patron' => $patron,
            'patrones_buscados' => $patrones,
            'total_archivos' => count($files),
        ]);

        return null;
    }
}
