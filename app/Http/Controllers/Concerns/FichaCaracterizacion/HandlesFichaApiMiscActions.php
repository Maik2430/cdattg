<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;

trait HandlesFichaApiMiscActions
{
    /**
     * Obtiene la cantidad de aprendices asociados a una ficha de caracterización por su ID.
     *
     * @param  int  $id  El ID de la ficha de caracterización.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con la cantidad de aprendices.
     */
    public function getCantidadAprendicesPorFicha($id)
    {
        try {
            // Se asume que existe una relación 'aprendices' en el modelo FichaCaracterizacion
            $ficha = FichaCaracterizacion::withCount('aprendices')->find($id);

            if (! $ficha) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ficha de caracterización no encontrada',
                    'cantidad_aprendices' => 0,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cantidad de aprendices obtenida exitosamente',
                'ficha_id' => $ficha->id,
                'cantidad_aprendices' => $ficha->aprendices_count,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la cantidad de aprendices',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
