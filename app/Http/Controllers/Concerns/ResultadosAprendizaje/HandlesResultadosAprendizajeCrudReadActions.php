<?php

namespace App\Http\Controllers\Concerns\ResultadosAprendizaje;

use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesResultadosAprendizajeCrudReadActions
{
    use HandlesResultadosAprendizajeQueryFilterHelpers;

    public function index(Request $request)
    {
        try {
            $query = ResultadosAprendizaje::with(['competencias', 'userCreate', 'userEdit']);
            $this->applyResultadosAprendizajeListFilters($query, $request);

            $resultadosAprendizaje = $query->paginate(10)->withQueryString();
            $competencias = Competencia::orderBy('nombre')->get();

            return view('resultados_aprendizaje.index', compact('resultadosAprendizaje', 'competencias'));
        } catch (Exception $e) {
            Log::error('Error al obtener lista de resultados de aprendizaje: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cargar los resultados de aprendizaje.');
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $query = ResultadosAprendizaje::with(['competencias', 'userCreate', 'userEdit']);
            $this->applyResultadosAprendizajeListFilters($query, $request, ajaxSearch: true);

            $perPage = $request->get('per_page', 10);
            $resultadosAprendizaje = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $resultadosAprendizaje->items(),
                'pagination' => [
                    'total' => $resultadosAprendizaje->total(),
                    'per_page' => $resultadosAprendizaje->perPage(),
                    'current_page' => $resultadosAprendizaje->currentPage(),
                    'last_page' => $resultadosAprendizaje->lastPage(),
                    'from' => $resultadosAprendizaje->firstItem(),
                    'to' => $resultadosAprendizaje->lastItem(),
                ],
                'filters' => $request->except(['page', 'per_page']),
            ]);
        } catch (Exception $e) {
            Log::error('Error en búsqueda de resultados de aprendizaje: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al realizar la búsqueda',
            ], 500);
        }
    }

    public function show(ResultadosAprendizaje $resultadoAprendizaje)
    {
        try {
            $resultadoAprendizaje->load(['competencias', 'guiasAprendizaje', 'userCreate', 'userEdit']);

            return view('resultados_aprendizaje.show', compact('resultadoAprendizaje'));
        } catch (Exception $e) {
            Log::error('Error al mostrar resultado de aprendizaje: '.$e->getMessage(), [
                'resultado_id' => $resultadoAprendizaje->id,
            ]);

            return redirect()->back()->with('error', 'Error al cargar el resultado de aprendizaje.');
        }
    }
}
