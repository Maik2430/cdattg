<?php

namespace App\Http\Controllers\Concerns\PersonaIngresoSalida;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaIngresoSalidaEstadisticasActions
{
    /**
     * Obtener estadísticas de personas dentro del edificio
     */
    public function estadisticasPersonasDentro(Request $request): JsonResponse
    {
        try {
            $sedeId = $request->query('sede_id'); // Opcional: filtrar por sede

            $estadisticas = $this->personaIngresoSalidaService->obtenerEstadisticasPersonasDentro(
                $sedeId ? (int) $sedeId : null
            );

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de personas dentro HOY
     */
    public function estadisticasPersonasDentroHoy(Request $request): JsonResponse
    {
        try {
            $sedeId = $request->query('sede_id'); // Opcional: filtrar por sede

            $estadisticas = $this->personaIngresoSalidaService->obtenerEstadisticasPersonasDentroHoy(
                $sedeId ? (int) $sedeId : null
            );

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de hoy: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }

    /**
     * Obtener lista de personas dentro actualmente
     */
    public function personasDentro(Request $request): JsonResponse
    {
        try {
            $sedeId = $request->query('sede_id'); // Opcional: filtrar por sede
            $tipoPersona = $request->query('tipo_persona'); // Opcional: filtrar por tipo

            $personas = $this->personaIngresoSalidaService->obtenerPersonasDentro(
                $sedeId ? (int) $sedeId : null,
                $tipoPersona
            );

            return response()->json([
                'success' => true,
                'data' => $personas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo personas dentro: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener personas dentro',
            ], 500);
        }
    }

    /**
     * Obtener estadísticas por fecha
     */
    public function estadisticasPorFecha(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fecha' => 'required|date',
                'sede_id' => 'nullable|integer|exists:sedes,id',
            ]);

            $estadisticas = $this->personaIngresoSalidaService->obtenerEstadisticasPorFecha(
                $validated['fecha'],
                $validated['sede_id'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas por fecha: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }

    /**
     * Obtener estadísticas por sede
     */
    public function estadisticasPorSede(Request $request, int $sedeId): JsonResponse
    {
        try {
            $estadisticas = $this->personaIngresoSalidaService->obtenerEstadisticasPorSede($sedeId);

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas por sede: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }
}
