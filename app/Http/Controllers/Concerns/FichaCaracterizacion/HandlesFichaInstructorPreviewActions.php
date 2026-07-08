<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesFichaInstructorPreviewActions
{
    /**
     * Genera preview de fechas efectivas para un instructor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function previewFechasInstructor(Request $request, string $fichaId, string $instructorFichaId)
    {
        try {
            $instructorFicha = \App\Models\InstructorFichaCaracterizacion::with('ficha')->findOrFail($instructorFichaId);

            $diasService = app(\App\Services\InstructorFichaDiasService::class);
            $fechasEfectivas = $diasService->generarFechasEfectivas($instructorFicha, $request->dias ?? []);

            return response()->json([
                'success' => true,
                'fechas_efectivas' => $fechasEfectivas,
                'total_sesiones' => count($fechasEfectivas),
            ]);

        } catch (\Exception $e) {
            Log::error('Error al generar preview de fechas', [
                'ficha_id' => $fichaId,
                'instructor_ficha_id' => $instructorFichaId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar preview de fechas',
            ], 500);
        }
    }
}
