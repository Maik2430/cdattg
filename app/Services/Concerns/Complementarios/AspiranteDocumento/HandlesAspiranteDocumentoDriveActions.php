<?php

namespace App\Services\Concerns\Complementarios\AspiranteDocumento;

use App\Exceptions\Complementarios\GoogleDriveException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HandlesAspiranteDocumentoDriveActions
{
    public function getGoogleDriveFiles(): array
    {
        try {
            $files = Storage::disk('google')->files('documentos_aspirantes');
            Log::info('Total de archivos en Google Drive: '.count($files));

            return $files;
        } catch (Exception $e) {
            Log::error('Error al listar archivos en Google Drive: '.$e->getMessage());
            throw new GoogleDriveException('Error al acceder a Google Drive: '.$e->getMessage(), 0, $e);
        }
    }
}
