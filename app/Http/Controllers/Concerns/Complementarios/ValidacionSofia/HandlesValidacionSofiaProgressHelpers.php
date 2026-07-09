<?php

namespace App\Http\Controllers\Concerns\Complementarios\ValidacionSofia;

use App\Jobs\Complementarios\ValidarSofiaJob;
use App\Models\Complementarios\SofiaValidationProgress;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait HandlesValidacionSofiaProgressHelpers
{
    private const PROGRAMA_NO_ENCONTRADO = 'Programa no encontrado.';

    /**
     * Crear registro de progreso
     */
    private function createProgressRecord($complementarioId, int $aspirantesCount): SofiaValidationProgress
    {
        $progress = SofiaValidationProgress::create([
            'complementario_id' => $complementarioId,
            'user_id' => auth()->id(),
            'status' => 284, // PENDING = 284 según ParametroSeeder
            'total_aspirantes' => $aspirantesCount,
            'processed_aspirantes' => 0,
            'successful_validations' => 0,
            'failed_validations' => 0,
        ]);

        Log::info('Registro de progreso creado', [
            'progress_id' => $progress->id,
            'total_aspirantes' => $aspirantesCount,
        ]);

        return $progress;
    }

    /**
     * Despachar job de validación
     */
    private function dispatchValidationJob($complementarioId, int $progressId): void
    {
        ValidarSofiaJob::dispatch($complementarioId, auth()->id(), $progressId)
            ->onQueue('sofia-validation');

        Log::info('Job despachado a la cola', [
            'job_class' => ValidarSofiaJob::class,
            'queue' => 'sofia-validation',
            'delay' => 2,
        ]);
    }

    /**
     * Manejar todas las excepciones
     */
    private function handleException($complementarioId, Exception $e): JsonResponse
    {
        if ($e instanceof ModelNotFoundException) {
            Log::error("Programa no encontrado: {$complementarioId}", ['exception' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => self::PROGRAMA_NO_ENCONTRADO,
            ], 404);
        }

        Log::error('Error iniciando validación SenaSofiaPlus', [
            'complementario_id' => $complementarioId,
            'user_id' => auth()->id(),
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error interno del servidor. Por favor, intente nuevamente.',
        ], 500);
    }
}
