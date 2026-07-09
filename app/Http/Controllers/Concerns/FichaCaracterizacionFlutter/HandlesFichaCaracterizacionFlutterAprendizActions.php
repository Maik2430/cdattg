<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacionFlutter;

use App\Models\FichaCaracterizacion;

trait HandlesFichaCaracterizacionFlutterAprendizActions
{
    use HandlesFichaCaracterizacionFlutterQueryHelpers;

    /**
     * Obtener cantidad de aprendices por ficha
     */
    public function getCantidadAprendicesPorFicha($fichaId)
    {
        try {
            $ficha = FichaCaracterizacion::findOrFail($fichaId);
            $cantidad = $ficha->aprendices()->count();

            return response()->json([
                'success' => true,
                'ficha_id' => $fichaId,
                'cantidad_aprendices' => $cantidad,
            ]);
        } catch (\Exception $e) {
            return $this->buildFichaCaracterizacionErrorResponse(
                'Error al obtener cantidad de aprendices',
                $e
            );
        }
    }

    /**
     * Obtener todas las fichas con la cantidad de aprendices
     */
    public function getAllFichasConAprendices()
    {
        try {
            $fichas = FichaCaracterizacion::all()->map(function ($ficha) {
                return [
                    'id' => $ficha->id,
                    'ficha' => $ficha->ficha,
                    'jornada_id' => $ficha->jornada_id,
                    'total_aprendices' => $ficha->aprendices()->count(),
                ];
            });

            return response()->json($fichas, 200);

        } catch (\Exception $e) {
            return $this->buildFichaCaracterizacionErrorResponse(
                'Error al obtener las fichas con cantidad de aprendices',
                $e
            );
        }
    }
}
