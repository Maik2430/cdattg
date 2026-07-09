<?php

namespace App\Jobs\Concerns\ValidarDocumento;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HandlesValidarDocumentoJobDriveHelpers
{
    private function validarDocumentoEnDrive($persona): bool
    {
        try {
            $tipoDocumento = $persona->tipoDocumento ? str_replace(' ', '_', $persona->tipoDocumento->name) : 'DOC';
            $numeroDocumento = $persona->numero_documento;
            $primerNombre = str_replace(' ', '_', $persona->primer_nombre);
            $primerApellido = str_replace(' ', '_', $persona->primer_apellido);

            $files = Storage::disk('google')->files('documentos_aspirantes');

            foreach ($files as $file) {
                $fileName = basename($file);
                if (strpos($fileName, "{$tipoDocumento}_{$numeroDocumento}_{$primerNombre}_{$primerApellido}_") === 0) {
                    if (Storage::disk('google')->exists($file)) {
                        Log::debug("✅ Documento encontrado en Google Drive: {$fileName}");

                        return true;
                    }
                }
            }

            Log::debug("❌ Documento no encontrado en Google Drive para: {$tipoDocumento}_{$numeroDocumento}_{$primerNombre}_{$primerApellido}");

            return false;
        } catch (\Exception $e) {
            Log::error('❌ Error al buscar documento en Google Drive', [
                'persona_id' => $persona->id,
                'numero_documento' => $persona->numero_documento,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
