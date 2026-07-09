<?php

namespace App\Http\Controllers\Concerns\Complementarios\ValidacionSofia;

use App\Models\Complementarios\ComplementarioOfertado;
use App\Models\Complementarios\SofiaValidationProgress;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait HandlesValidacionSofiaActions
{
    /**
     * Iniciar validación SOFIA para un programa complementario
     */
    public function validarSofia($complementarioId): JsonResponse
    {
        try {
            Log::info('Iniciando solicitud de validación SenaSofiaPlus', [
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'timestamp' => now(),
            ]);

            $programa = ComplementarioOfertado::findOrFail($complementarioId);
            Log::info("Programa encontrado: {$programa->nombre}");

            $validationResult = $this->performValidations($complementarioId);
            if ($validationResult['error'] !== null) {
                return $validationResult['error'];
            }

            $aspirantesCount = $validationResult['aspirantes_count'];
            $progress = $this->createProgressRecord($complementarioId, $aspirantesCount);
            $this->dispatchValidationJob($complementarioId, $progress->id);

            return response()->json([
                'success' => true,
                'message' => "Validación iniciada para {$aspirantesCount} aspirantes. El proceso se ejecutará en segundo plano.",
                'aspirantes_count' => $aspirantesCount,
                'progress_id' => $progress->id,
            ]);
        } catch (Exception $e) {
            return $this->handleException($complementarioId, $e);
        }
    }

    /**
     * Obtener el progreso de una validación
     */
    public function getValidationProgress($progressId): JsonResponse
    {
        try {
            $progress = SofiaValidationProgress::with('complementario')->findOrFail($progressId);

            return response()->json([
                'success' => true,
                'progress' => [
                    'id' => $progress->id,
                    'status' => $progress->status,
                    'status_label' => $progress->status_label,
                    'total_aspirantes' => $progress->total_aspirantes,
                    'processed_aspirantes' => $progress->processed_aspirantes,
                    'successful_validations' => $progress->successful_validations,
                    'failed_validations' => $progress->failed_validations,
                    'progress_percentage' => $progress->progress_percentage,
                    'started_at' => $progress->started_at?->format('d/m/Y H:i:s'),
                    'completed_at' => $progress->completed_at?->format('d/m/Y H:i:s'),
                    'errors' => $progress->errors,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Error al obtener progreso de validación SOFIA', [
                'progress_id' => $progressId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el progreso de la validación.',
            ], 500);
        }
    }
}
