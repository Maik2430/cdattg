<?php

namespace App\Services\Concerns\Complementarios\AspiranteManagement;

use App\Services\Complementarios\AspiranteDocumentoService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesAspiranteManagementDocumentValidationActions
{
    public function validarDocumentos(int $complementarioId, AspiranteDocumentoService $documentoService): array
    {
        try {
            $errorResponse = $this->validarDocumentosPrecondiciones($complementarioId);
            if ($errorResponse !== null) {
                return $errorResponse;
            }

            $aspirantes = $this->aspiranteRepository->findByPrograma($complementarioId, ['persona.tipoDocumento']);
            $files = $documentoService->getGoogleDriveFiles();
            $resultados = $this->procesarValidacionDocumentos($aspirantes, $files, $documentoService);

            Log::info('Validación de documentos completada', [
                'complementario_id' => $complementarioId,
                'total' => $resultados['total'],
                'con_documento' => $resultados['con_documento'],
                'sin_documento' => $resultados['sin_documento'],
                'errores' => $resultados['errores'],
            ]);

            return [
                'success' => true,
                'message' => "Validación completada. Total: {$resultados['total']}, ".
                    "Con documento: {$resultados['con_documento']}, ".
                    "Sin documento: {$resultados['sin_documento']}".
                    ($resultados['errores'] > 0 ? ", Errores: {$resultados['errores']}" : ''),
                'total' => $resultados['total'],
                'con_documento' => $resultados['con_documento'],
                'sin_documento' => $resultados['sin_documento'],
                'errores' => $resultados['errores'],
            ];

        } catch (Exception $e) {
            Log::error('Error validando documentos: '.$e->getMessage(), [
                'complementario_id' => $complementarioId,
                'user_id' => Auth::id(),
                'exception' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Error interno del servidor: '.$e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    private function procesarValidacionDocumentos(Collection $aspirantes, array $files, AspiranteDocumentoService $documentoService): array
    {
        $totalAspirantes = $aspirantes->count();
        $conDocumento = 0;
        $sinDocumento = 0;
        $errores = 0;

        foreach ($aspirantes as $aspirante) {
            try {
                $persona = $aspirante->persona;
                $patron = $documentoService->construirPatronBusqueda($persona);
                $tieneDocumento = $documentoService->buscarDocumentoEnGoogleDrive($files, $patron);

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
