<?php

namespace App\Http\Controllers\Concerns\Competencia;

use App\Models\Competencia;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesCompetenciaCrudReadActions
{
    use HandlesCompetenciaQueryFilterHelpers;

    public function index()
    {
        try {
            return view('competencias.index');
        } catch (Exception $e) {
            Log::error('Error al cargar vista de competencias: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cargar competencias.');
        }
    }

    public function show($id)
    {
        try {
            $competencia = Competencia::with(['userCreate', 'userEdit', 'programasFormacion', 'resultadosCompetencia'])
                ->findOrFail($id);

            return view('competencias.show', compact('competencia'));
        } catch (Exception $e) {
            Log::error('Error al mostrar competencia: '.$e->getMessage());

            return redirect()->route('competencias.index')->with('error', 'Competencia no encontrada');
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $query = Competencia::with(['userCreate', 'userEdit', 'programasFormacion']);
            $this->applyCompetenciaSearchFilters($query, $request);

            $perPage = $request->get('per_page', 10);
            $competencias = $query->paginate($perPage);

            $data = $competencias->map(fn (Competencia $competencia) => $this->formatCompetenciaForSearch($competencia));

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $competencias->total(),
                    'per_page' => $competencias->perPage(),
                    'current_page' => $competencias->currentPage(),
                    'last_page' => $competencias->lastPage(),
                    'from' => $competencias->firstItem(),
                    'to' => $competencias->lastItem(),
                ],
                'filters' => $request->except(['page', 'per_page']),
            ]);
        } catch (Exception $e) {
            Log::error('Error en búsqueda de competencias: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al realizar la búsqueda',
            ], 500);
        }
    }
}
