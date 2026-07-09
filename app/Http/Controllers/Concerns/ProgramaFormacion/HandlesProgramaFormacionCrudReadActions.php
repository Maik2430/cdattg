<?php

namespace App\Http\Controllers\Concerns\ProgramaFormacion;

use App\Models\ProgramaFormacion;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

trait HandlesProgramaFormacionCrudReadActions
{
    use HandlesProgramaFormacionQueryFilterHelpers;

    public function index()
    {
        $programas = $this->programaService->listar(15);

        return view('programas.index', compact('programas'));
    }

    public function show(string $id)
    {
        $programa = ProgramaFormacion::with([
            'redConocimiento',
            'nivelFormacion',
            'userCreated',
            'userEdited',
            'competencias' => function ($query) {
                $query->orderBy('nombre');
            },
        ])->findOrFail($id);

        return view('programas.show', compact('programa'));
    }

    public function search(Request $request): JsonResponse|RedirectResponse|View
    {
        try {
            $query = $request->input('search');
            $perPage = $request->input('per_page', 6);

            $programasQuery = ProgramaFormacion::with(['redConocimiento', 'nivelFormacion', 'userCreated', 'userEdited']);
            $this->applyProgramaFormacionSearchFilters($programasQuery, $request);

            $programas = $programasQuery->paginate($perPage);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'programas' => $programas->items(),
                        'pagination' => [
                            'current_page' => $programas->currentPage(),
                            'last_page' => $programas->lastPage(),
                            'per_page' => $programas->perPage(),
                            'total' => $programas->total(),
                            'has_more_pages' => $programas->hasMorePages(),
                        ],
                        'filters' => [
                            'search' => $query,
                            'red_conocimiento_id' => $request->input('red_conocimiento_id'),
                            'nivel_formacion_id' => $request->input('nivel_formacion_id'),
                            'status' => $request->input('status'),
                        ],
                    ],
                ]);
            }

            if ($programas->count() == 0 && ! empty($query)) {
                Log::info('Búsqueda de programas sin resultados', [
                    'query' => $query,
                    'filters' => $request->all(),
                    'usuario_id' => Auth::id(),
                ]);

                return redirect()->route('programa.index')
                    ->with('error', 'No se encontraron programas de formación con los criterios especificados.');
            }

            Log::info('Búsqueda de programas realizada', [
                'query' => $query,
                'filters' => $request->all(),
                'resultados' => $programas->count(),
                'usuario_id' => Auth::id(),
            ]);

            return view('programas.index', compact('programas'));
        } catch (Exception $e) {
            Log::error('Error en búsqueda de programas', [
                'filters' => $request->all(),
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno en la búsqueda.',
                ], 500);
            }

            return redirect()->route('programa.index')->with('error', 'Error interno en la búsqueda.');
        }
    }
}
