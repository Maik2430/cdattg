<?php

namespace App\Http\Controllers\Complementarios\Concerns;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait HandlesAspiranteComplementarioReadActions
{
    public function index(): View
    {
        $programas = $this->aspiranteManagementService->obtenerProgramasParaGestion();

        return view('complementarios.aspirantes.index', compact('programas'));
    }

    public function verAspirantes(string $curso): View
    {
        $data = $this->aspiranteManagementService->obtenerAspirantesPorPrograma($curso);

        return view('complementarios.aspirantes.programa', $data);
    }

    public function programa(int $programa): View
    {
        try {
            $data = $this->aspiranteManagementService->obtenerAspirantesPorProgramaId($programa);

            return view('complementarios.aspirantes.programa', $data);
        } catch (Exception $e) {
            Log::error('Error en programa() método: '.$e->getMessage(), [
                'programa_id' => $programa,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function getEstadisticasExclusion(int $complementarioId): JsonResponse
    {
        try {
            $estadisticas = $this->aspiranteRepository->getEstadisticasExclusion($complementarioId);

            return response()->json([
                'success' => true,
                'estadisticas' => $estadisticas,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas de exclusión: '.$e->getMessage(),
            ], 500);
        }
    }
}
