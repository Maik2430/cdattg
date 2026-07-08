<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesFichaApiEstadisticasActions
{
    /**
     * Obtiene estadísticas generales de las fichas de caracterización.
     *
     * @return \Illuminate\Http\JsonResponse Estadísticas de fichas de caracterización.
     */
    public function getEstadisticasFichas()
    {
        try {
            Log::info('Solicitud de estadísticas de fichas de caracterización', [
                'user_id' => Auth::id(),
                'timestamp' => now(),
            ]);

            $totalFichas = FichaCaracterizacion::count();
            $fichasActivas = FichaCaracterizacion::where('status', 1)->count();
            $fichasInactivas = FichaCaracterizacion::where('status', 0)->count();
            $fichasConAprendices = FichaCaracterizacion::has('aprendices')->count();
            $fichasSinAprendices = FichaCaracterizacion::doesntHave('aprendices')->count();
            $totalAprendices = FichaCaracterizacion::withCount('aprendices')->get()->sum('aprendices_count');

            $estadisticas = [
                'total_fichas' => $totalFichas,
                'fichas_activas' => $fichasActivas,
                'fichas_inactivas' => $fichasInactivas,
                'fichas_con_aprendices' => $fichasConAprendices,
                'fichas_sin_aprendices' => $fichasSinAprendices,
                'total_aprendices' => $totalAprendices,
                'promedio_aprendices_por_ficha' => $fichasConAprendices > 0 ? round($totalAprendices / $fichasConAprendices, 2) : 0,
            ];

            Log::info('Estadísticas de fichas calculadas exitosamente', [
                'estadisticas' => $estadisticas,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas exitosamente',
                'data' => $estadisticas,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al calcular estadísticas de fichas', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las estadísticas de fichas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
