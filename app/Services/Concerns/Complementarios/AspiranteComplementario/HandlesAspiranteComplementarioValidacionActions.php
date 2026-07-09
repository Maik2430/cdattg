<?php

namespace App\Services\Concerns\Complementarios\AspiranteComplementario;

use Exception;
use Illuminate\Support\Facades\Log;

trait HandlesAspiranteComplementarioValidacionActions
{
    /**
     * Procesar validación de documentos
     */
    public function procesarValidacionDocumentos($aspirantes, $files): array
    {
        $totalAspirantes = $aspirantes->count();
        $conDocumento = 0;
        $sinDocumento = 0;
        $errores = 0;

        foreach ($aspirantes as $aspirante) {
            try {
                $persona = $aspirante->persona;
                $patron = $this->documentoService->construirPatronBusqueda($persona);

                $tieneDocumento = $this->documentoService->buscarDocumentoEnGoogleDrive($files, $patron);

                $this->personaRepository->updateDocumentoStatus($persona, $tieneDocumento);

                if ($tieneDocumento) {
                    $conDocumento++;
                } else {
                    $sinDocumento++;
                }
            } catch (Exception $e) {
                $errores++;
                Log::error("Error validando documento para aspirante {$aspirante->id}", [
                    'aspirante_id' => $aspirante->id,
                    'persona_id' => $aspirante->persona_id,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return [
            'total' => $totalAspirantes,
            'con_documento' => $conDocumento,
            'sin_documento' => $sinDocumento,
            'errores' => $errores,
        ];
    }
}
