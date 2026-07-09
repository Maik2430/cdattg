<?php

namespace App\Http\Controllers\Concerns\GuiaAprendizaje;

use App\Models\Competencia;
use App\Models\GuiasAprendizaje;
use App\Models\ResultadosAprendizaje;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesGuiaAprendizajeCrudReadActions
{
    use HandlesGuiaAprendizajeQueryFilterHelpers;

    public function index(Request $request)
    {
        try {
            $query = GuiasAprendizaje::with(['resultadosAprendizaje.competencias', 'actividades', 'userCreate', 'userEdit']);
            $this->applyGuiaAprendizajeListFilters($query, $request);

            $guiasAprendizaje = $query->paginate(10)->withQueryString();
            $competencias = Competencia::orderBy('nombre')->get();
            $resultadosAprendizaje = ResultadosAprendizaje::orderBy('codigo')->get();
            $usuarios = User::with('persona')->get();

            return view('guias_aprendizaje.index', compact(
                'guiasAprendizaje',
                'competencias',
                'resultadosAprendizaje',
                'usuarios'
            ));
        } catch (Exception $e) {
            Log::error('Error al obtener lista de guías de aprendizaje: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cargar las guías de aprendizaje.');
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $query = GuiasAprendizaje::with(['resultadosAprendizaje.competencias', 'userCreate', 'userEdit']);
            $this->applyGuiaAprendizajeListFilters($query, $request, ajaxSearch: true);

            $guiasAprendizaje = $query->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $guiasAprendizaje->items(),
                'pagination' => [
                    'current_page' => $guiasAprendizaje->currentPage(),
                    'last_page' => $guiasAprendizaje->lastPage(),
                    'per_page' => $guiasAprendizaje->perPage(),
                    'total' => $guiasAprendizaje->total(),
                    'from' => $guiasAprendizaje->firstItem(),
                    'to' => $guiasAprendizaje->lastItem(),
                ],
                'links' => $guiasAprendizaje->links()->toHtml(),
            ]);
        } catch (Exception $e) {
            Log::error('Error en búsqueda de guías de aprendizaje: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al realizar la búsqueda',
            ], 500);
        }
    }

    public function show(GuiasAprendizaje $guiaAprendizaje)
    {
        try {
            $guiaAprendizaje->load(['resultadosAprendizaje', 'actividades']);

            return view('guias_aprendizaje.show', compact('guiaAprendizaje'));
        } catch (Exception $e) {
            Log::error('Error al mostrar guía de aprendizaje: '.$e->getMessage(), [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->with('error', 'Error al cargar la guía de aprendizaje.');
        }
    }
}
