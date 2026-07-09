<?php

namespace App\Http\Controllers\Concerns\Complementarios\ValidacionSofia;

use App\Models\Complementarios\AspiranteComplementario;
use App\Models\Complementarios\SofiaValidationProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait HandlesValidacionSofiaValidationHelpers
{
    /**
     * Realizar todas las validaciones necesarias antes de iniciar el proceso
     */
    private function performValidations($complementarioId): array
    {
        $aspirantesCount = $this->countAspirantesNeedingValidation($complementarioId);

        $errorResponse = $this->checkAspirantesCount($aspirantesCount, $complementarioId);
        if ($errorResponse !== null) {
            return ['error' => $errorResponse, 'aspirantes_count' => 0];
        }

        $errorResponse = $this->checkExistingProgress($complementarioId);
        if ($errorResponse !== null) {
            return ['error' => $errorResponse, 'aspirantes_count' => 0];
        }

        return ['error' => null, 'aspirantes_count' => $aspirantesCount];
    }

    /**
     * Contar aspirantes que necesitan validación
     */
    private function countAspirantesNeedingValidation($complementarioId): int
    {
        $count = AspiranteComplementario::with('persona')
            ->where('complementario_id', $complementarioId)
            ->whereHas('persona', function ($query): void {
                $query->whereIn('estado_sofia', [277, 279]); // NO REGISTRADO (277) o REQUIERE CAMBIO (279)
            })
            ->count();

        Log::info("Aspirantes que necesitan validación: {$count}");

        return $count;
    }

    /**
     * Verificar si hay aspirantes para validar
     */
    private function checkAspirantesCount(int $aspirantesCount, $complementarioId): ?JsonResponse
    {
        if ($aspirantesCount === 0) {
            Log::warning("No hay aspirantes que necesiten validación para programa {$complementarioId}");

            return response()->json([
                'success' => false,
                'message' => 'No hay aspirantes que necesiten validación en este programa.',
            ]);
        }

        return null;
    }

    /**
     * Verificar si ya hay una validación en progreso
     */
    private function checkExistingProgress($complementarioId): ?JsonResponse
    {
        $existingProgress = SofiaValidationProgress::where('complementario_id', $complementarioId)
            ->whereIn('status', [284, 285]) // PENDING (284) o PROCESSING (285)
            ->first();

        if ($existingProgress) {
            Log::warning("Ya existe una validación en progreso para programa {$complementarioId}", [
                'progress_id' => $existingProgress->id,
                'status' => $existingProgress->status,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ya hay una validación en progreso para este programa. Espere a que termine.',
            ]);
        }

        return null;
    }
}
