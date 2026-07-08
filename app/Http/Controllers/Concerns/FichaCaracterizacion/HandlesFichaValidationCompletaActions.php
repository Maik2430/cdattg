<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Services\FichaCaracterizacionValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesFichaValidationCompletaActions
{
    /**
     * Valida una ficha de caracterización usando el servicio de validación.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validarFicha(Request $request)
    {
        try {
            $validator = new FichaCaracterizacionValidationService;

            $datos = $request->all();
            $excluirFichaId = $request->input('excluir_ficha_id');

            $resultado = $validator->validarFichaCompleta($datos, $excluirFichaId);

            return response()->json($resultado);

        } catch (\Exception $e) {
            Log::error('Error al validar ficha', [
                'error' => $e->getMessage(),
                'datos' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'valido' => false,
                'mensaje' => 'Error interno al validar la ficha.',
                'errores' => ['Error interno en la validación'],
                'advertencias' => [],
            ], 500);
        }
    }
}
