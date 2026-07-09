<?php

namespace App\Services\Concerns\Complementarios\AspiranteManagement;

use App\Exceptions\ProcesarDocumentoIdentidadException;
use App\Models\Complementarios\AspiranteComplementario;
use App\Models\Persona;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

trait HandlesAspiranteManagementDocumentStorageActions
{
    public function almacenarDocumentoIdentidad(AspiranteComplementario $aspirante, Persona $persona, UploadedFile $archivo): void
    {
        try {
            $upload = $this->documentoService->subirDocumentoIdentidad($persona, $archivo);

            Log::info('Documento de identidad cargado manualmente', [
                'aspirante_id' => $aspirante->id,
                'persona_id' => $persona->id,
                'file_name' => $upload['name'],
            ]);

            $this->aspiranteRepository->update($aspirante, [
                'documento_identidad_path' => $upload['path'],
                'documento_identidad_nombre' => $upload['name'],
            ]);
        } catch (Exception $e) {
            Log::error('Error al guardar el documento de identidad del aspirante', [
                'aspirante_id' => $aspirante->id,
                'persona_id' => $persona->id,
                'exception' => $e->getMessage(),
            ]);

            $this->aspiranteRepository->update($aspirante, ['estado' => 1]);

            throw new ProcesarDocumentoIdentidadException('Error al procesar el documento de identidad');
        }
    }
}
