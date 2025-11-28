<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplementarioApiController extends Controller
{
    /**
     * Obtener RAPs por competencias seleccionadas
     */
    public function getRapsByCompetencias(Request $request): JsonResponse
    {
        $competenciasIds = $request->get('competencias', []);

        if (empty($competenciasIds)) {
            return response()->json([]);
        }

        // Convertir a array si es string
        if (is_string($competenciasIds)) {
            $competenciasIds = explode(',', $competenciasIds);
        }

        // Obtener RAPs asociados a las competencias seleccionadas
        $raps = ResultadosAprendizaje::query()
            ->whereHas('competencias', function ($query) use ($competenciasIds) {
                $query->whereIn('competencias.id', $competenciasIds);
            })
            ->activos()
            ->with(['competencias' => function ($query) {
                $query->select('competencias.id', 'competencias.codigo', 'competencias.nombre');
            }])
            ->get(['id', 'codigo', 'nombre'])
            ->map(function ($rap) {
                return [
                    'id' => $rap->id,
                    'codigo' => $rap->codigo,
                    'nombre' => $rap->nombre,
                    'competencia_nombre' => $rap->competencias->first()?->nombre ?? 'Sin competencia',
                ];
            });

        return response()->json($raps);
    }

    /**
     * Obtener competencias disponibles
     */
    public function getCompetencias(): JsonResponse
    {
        $competencias = Competencia::query()
            ->activos()
            ->ordenadoPorCodigo()
            ->get(['id', 'codigo', 'nombre']);

        return response()->json($competencias);
    }

    /**
     * Obtener guías de aprendizaje disponibles
     */
    public function getGuiasAprendizaje(): JsonResponse
    {
        $guias = \App\Models\GuiasAprendizaje::query()
            ->activas()
            ->porNombreAsc()
            ->get(['id', 'codigo', 'nombre']);

        return response()->json($guias);
    }
}
