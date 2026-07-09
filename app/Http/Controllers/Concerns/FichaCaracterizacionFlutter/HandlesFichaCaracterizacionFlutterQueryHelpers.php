<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacionFlutter;

trait HandlesFichaCaracterizacionFlutterQueryHelpers
{
    /**
     * @return array<int, string>
     */
    protected function getFichaCaracterizacionBaseSelect(): array
    {
        return [
            'id', 'ficha', 'fecha_inicio', 'fecha_fin', 'total_horas',
            'status', 'programa_formacion_id', 'instructor_id', 'sede_id',
            'modalidad_formacion_id', 'jornada_id', 'ambiente_id',
        ];
    }

    protected function buildFichaCaracterizacionErrorResponse(string $message, \Exception $e, int $status = 500): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => $e->getMessage(),
        ], $status);
    }
}
