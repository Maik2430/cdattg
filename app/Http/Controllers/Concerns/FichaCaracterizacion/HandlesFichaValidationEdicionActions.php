<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Services\FichaCaracterizacionValidationService;
use Illuminate\Support\Facades\Log;

trait HandlesFichaValidationEdicionActions
{
    /**
     * Valida si una ficha puede ser eliminada.
     *
     * @param  int  $id  ID de la ficha
     * @return \Illuminate\Http\JsonResponse
     */
    public function validarEliminacionFicha(string $id)
    {
        try {
            $validator = new FichaCaracterizacionValidationService;
            $resultado = $validator->validarEliminacionFicha($id);

            return response()->json($resultado);

        } catch (\Exception $e) {
            Log::error('Error al validar eliminación de ficha', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'valido' => false,
                'mensaje' => 'Error interno al validar eliminación de la ficha.',
                'errores' => ['Error interno en la validación'],
            ], 500);
        }
    }

    /**
     * Valida si una ficha puede ser editada.
     *
     * @param  int  $id  ID de la ficha
     * @return \Illuminate\Http\JsonResponse
     */
    public function validarEdicionFicha(string $id)
    {
        try {
            $validator = new FichaCaracterizacionValidationService;
            $resultado = $validator->validarEdicionFicha($id);

            return response()->json($resultado);

        } catch (\Exception $e) {
            Log::error('Error al validar edición de ficha', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'valido' => false,
                'mensaje' => 'Error interno al validar edición de la ficha.',
                'errores' => ['Error interno en la validación'],
                'advertencias' => [],
            ], 500);
        }
    }
}
