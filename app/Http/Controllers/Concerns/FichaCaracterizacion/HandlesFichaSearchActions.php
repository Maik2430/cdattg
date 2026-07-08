<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesFichaSearchActions
{
    public function search(Request $request)
    {
        try {
            Log::info('Búsqueda avanzada de fichas de caracterización', [
                'filters' => $request->all(),
                'user_id' => Auth::id(),
                'timestamp' => now(),
            ]);

            $query = $this->buildFichaSearchQuery($request);
            $perPage = $request->input('per_page', 10);
            $fichas = $query->paginate($perPage);

            Log::info('Búsqueda avanzada completada', [
                'filters_applied' => $request->all(),
                'resultados_encontrados' => $fichas->count(),
                'total_resultados' => $fichas->total(),
                'user_id' => Auth::id(),
            ]);

            if ($request->ajax()) {
                $fichasFormateadas = $fichas->map(fn ($ficha) => $this->formatFichaSearchJson($ficha));

                return response()->json([
                    'success' => true,
                    'data' => [
                        'fichas' => $fichasFormateadas,
                        'pagination' => [
                            'current_page' => $fichas->currentPage(),
                            'last_page' => $fichas->lastPage(),
                            'per_page' => $fichas->perPage(),
                            'total' => $fichas->total(),
                            'from' => $fichas->firstItem(),
                            'to' => $fichas->lastItem(),
                        ],
                    ],
                ]);
            }

            return view('fichas.index', compact('fichas'))
                ->with('filters', $request->all());
        } catch (\Exception $e) {
            Log::error('Error en búsqueda avanzada de fichas', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'filters' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al realizar la búsqueda',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Error al realizar la búsqueda. Por favor, intente nuevamente.');
        }
    }
}
