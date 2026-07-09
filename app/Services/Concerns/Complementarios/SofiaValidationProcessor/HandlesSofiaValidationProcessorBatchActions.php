<?php

namespace App\Services\Concerns\Complementarios\SofiaValidationProcessor;

use App\Models\Complementarios\SofiaValidationProgress;
use Illuminate\Support\Facades\Log;

trait HandlesSofiaValidationProcessorBatchActions
{
    /**
     * Procesar validaciones en lotes
     */
    public function processBatch(
        $aspirantes,
        int $complementarioId,
        ?SofiaValidationProgress $progress = null
    ): array {
        $totalAspirantes = $aspirantes->count();
        Log::info('Iniciando validacion de aspirantes', ['total' => $totalAspirantes]);

        $this->checkServiceHealth();

        $stats = [
            'exitosos' => 0,
            'errores' => 0,
            'errores_detalle' => [],
            'procesados' => 0,
        ];

        $batches = $aspirantes->chunk($this->batchSize);
        $totalBatches = $batches->count();

        foreach ($batches as $batchIndex => $batch) {
            $this->logBatchStart($batchIndex, $totalBatches, $batch->count());
            $this->processBatchItems($batch, $complementarioId, $progress, $totalAspirantes, $stats);
            $this->waitBetweenBatches($batchIndex, $totalBatches);
        }

        return [
            'total' => $totalAspirantes,
            'exitosos' => $stats['exitosos'],
            'errores' => $stats['errores'],
            'errores_detalle' => $stats['errores_detalle'],
        ];
    }

    /**
     * Procesar items de un lote
     */
    private function processBatchItems(
        $batch,
        int $complementarioId,
        ?SofiaValidationProgress $progress,
        int $totalAspirantes,
        array &$stats
    ): void {
        foreach ($batch as $aspirante) {
            $stats['procesados']++;
            $this->logAspiranteValidation($aspirante, $stats['procesados'], $totalAspirantes);

            $result = $this->validationService->validateAspirante(
                $aspirante,
                $complementarioId,
                $progress
            );

            $this->updateProgress($progress, $result);
            $this->updateStats($result, $stats);
            $this->applyDelayIfNeeded($stats['procesados'], $totalAspirantes);
        }
    }
}
