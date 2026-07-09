<?php

namespace App\Services\Concerns\Complementarios\SofiaValidationProcessor;

use App\Models\Complementarios\SofiaValidationProgress;
use Exception;
use Illuminate\Support\Facades\Log;

trait HandlesSofiaValidationProcessorStatsHelpers
{
    /**
     * Registrar inicio de procesamiento de lote
     */
    private function logBatchStart(int $batchIndex, int $totalBatches, int $batchSize): void
    {
        $batchNumber = $batchIndex + 1;
        Log::info('Procesando lote', [
            'lote' => $batchNumber,
            'total_lotes' => $totalBatches,
            'aspirantes_lote' => $batchSize,
        ]);
    }

    /**
     * Registrar validación de aspirante
     */
    private function logAspiranteValidation($aspirante, int $procesados, int $totalAspirantes): void
    {
        $cedula = $aspirante->persona->numero_documento;
        Log::info('Validando cedula', [
            'cedula' => $cedula,
            'progreso' => "{$procesados}/{$totalAspirantes}",
        ]);
    }

    /**
     * Actualizar progreso si existe
     */
    private function updateProgress(?SofiaValidationProgress $progress, array $result): void
    {
        if ($progress) {
            $isSuccessful = isset($result['success']) && $result['success'] === true;
            $progress->incrementProcessed($isSuccessful);
        }
    }

    /**
     * Actualizar estadísticas de procesamiento
     */
    private function updateStats(array $result, array &$stats): void
    {
        if ($result['success']) {
            $estado = $result['estado'] ?? null;
            if ($this->isValidState($estado)) {
                $stats['exitosos']++;
            }
        } else {
            $stats['errores']++;
            $stats['errores_detalle'][] = $result['error'] ?? 'Error desconocido';
        }
    }

    /**
     * Verificar si el estado es válido
     */
    private function isValidState(?int $estado): bool
    {
        return $estado !== null && in_array($estado, [0, 1, 2], true);
    }

    /**
     * Verificar que el servicio esté disponible al inicio del proceso
     */
    private function checkServiceHealth(): void
    {
        try {
            $this->validationService->checkServiceHealth();
            Log::info('Health check del servicio Playwright completado al inicio del proceso');
        } catch (Exception $e) {
            Log::warning('No se pudo verificar health del servicio Playwright al inicio', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
