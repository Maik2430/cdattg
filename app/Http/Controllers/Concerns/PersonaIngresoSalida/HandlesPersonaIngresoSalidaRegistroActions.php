<?php

namespace App\Http\Controllers\Concerns\PersonaIngresoSalida;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaIngresoSalidaRegistroActions
{
    /**
     * Registrar entrada de una persona
     */
    public function registrarEntrada(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'persona_id' => 'required|integer|exists:personas,id',
                'sede_id' => 'required|integer|exists:sedes,id',
                'ambiente_id' => 'nullable|integer|exists:ambientes,id',
                'ficha_caracterizacion_id' => 'nullable|integer|exists:fichas_caracterizacion,id',
                'observaciones' => 'nullable|string|max:1000',
            ]);

            $registro = $this->personaIngresoSalidaService->registrarEntrada(
                $validated['persona_id'],
                $validated['sede_id'],
                $validated['ambiente_id'] ?? null,
                $validated['ficha_caracterizacion_id'] ?? null,
                $validated['observaciones'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Entrada registrada correctamente',
                'data' => $registro->load(['persona', 'sede', 'ambiente', 'fichaCaracterizacion']),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error registrando entrada: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Registrar salida de una persona
     */
    public function registrarSalida(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'persona_id' => 'required|integer|exists:personas,id',
                'sede_id' => 'required|integer|exists:sedes,id',
                'observaciones' => 'nullable|string|max:1000',
            ]);

            $this->personaIngresoSalidaService->registrarSalida(
                $validated['persona_id'],
                $validated['sede_id'],
                $validated['observaciones'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Salida registrada correctamente',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error registrando salida: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
