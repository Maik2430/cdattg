<?php

namespace App\Services\Concerns\Complementarios\SofiaValidationProcessor;

use Illuminate\Support\Facades\Log;

trait HandlesSofiaValidationProcessorTimingHelpers
{
    /**
     * Aplicar delay si es necesario
     */
    private function applyDelayIfNeeded(int $procesados, int $totalAspirantes): void
    {
        $delay = $this->calculateDelay($procesados, $totalAspirantes);
        if ($delay > 0) {
            Log::debug('Esperando antes de siguiente validacion', ['delay_ms' => $delay]);
            usleep($delay * 1000);
        }
    }

    /**
     * Esperar entre lotes si es necesario
     */
    private function waitBetweenBatches(int $batchIndex, int $totalBatches): void
    {
        if ($totalBatches > 1 && $batchIndex < $totalBatches - 1) {
            Log::info('Cambio de lote - esperando', ['segundos' => $this->batchDelay]);
            sleep($this->batchDelay);
        }
    }

    /**
     * Calcular delay dinámico basado en el progreso
     */
    private function calculateDelay(int $procesados, int $total): int
    {
        $delay = 0;

        if ($total === 0) {
            return $delay;
        }

        $progress = $procesados / $total;

        if ($progress < self::PROGRESS_THRESHOLD_LOW) {
            $delay = self::DELAY_INITIAL_MS;
        } elseif ($progress < self::PROGRESS_THRESHOLD_MID) {
            $delay = self::DELAY_MID_MS;
        } else {
            $delay = self::DELAY_FINAL_MS;
        }

        return $delay;
    }
}
