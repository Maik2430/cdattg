<?php

namespace App\Http\Controllers\Concerns\Aprendiz;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait HandlesAprendizApiActions
{
    /**
     * Obtiene los aprendices asociados a una ficha específica.
     *
     * @param  int  $fichaId
     */
    public function getAprendicesByFicha($fichaId): JsonResponse
    {
        try {
            $aprendices = $this->aprendizService->obtenerPorFicha($fichaId);
            $datos = $this->formatAprendicesForApi($aprendices);

            return response()->json([
                'success' => true,
                'aprendices' => $datos,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener aprendices por ficha: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los aprendices de la ficha.',
            ], 500);
        }
    }

    /**
     * API endpoint para listar todos los aprendices.
     */
    public function apiIndex(): JsonResponse
    {
        try {
            $aprendices = $this->aprendizService->listarConFiltros(['per_page' => 1000]);
            $datos = $this->formatAprendicesForApi(collect($aprendices->items()));

            return response()->json([
                'success' => true,
                'aprendices' => $datos,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error en API de aprendices: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los aprendices.',
            ], 500);
        }
    }

    /**
     * Busca aprendices por término de búsqueda (nombre o documento).
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $termino = $request->input('q', '');
            $aprendices = $this->aprendizService->buscar($termino, 10);
            $datos = $this->formatAprendicesForApi($aprendices);

            return response()->json([
                'success' => true,
                'aprendices' => $datos,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al buscar aprendices: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al buscar aprendices.',
            ], 500);
        }
    }

    /**
     * @param  Collection<int, mixed>|\Illuminate\Database\Eloquent\Collection<int, mixed>  $aprendices
     */
    protected function formatAprendicesForApi(Collection|\Illuminate\Database\Eloquent\Collection $aprendices): Collection
    {
        return $aprendices->map(function ($aprendiz) {
            return $this->aprendizService->formatearParaApi($aprendiz);
        });
    }
}
