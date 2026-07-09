<?php

namespace App\Http\Controllers\Concerns\Aprendiz;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

trait HandlesAprendizCrudReadActions
{
    use HandlesAprendizFormDataHelpers;

    /**
     * Muestra un listado de aprendices con búsqueda y filtros.
     */
    public function index(Request $request): View|RedirectResponse
    {
        try {
            $filtros = $request->only(['search', 'ficha_id']);
            $filtros['per_page'] = 15;

            $aprendices = $this->aprendizService->listarConFiltros($filtros);
            $fichas = $this->getAprendizFichasActivas();
            $personas = $this->getAprendizPersonasDisponibles();

            return view('aprendices.index', compact('aprendices', 'fichas', 'personas'));
        } catch (Exception $e) {
            Log::error('Error al listar aprendices: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cargar el listado de aprendices.');
        }
    }

    /**
     * Muestra la información de un aprendiz específico.
     *
     * @param  int  $id
     */
    public function show($id): View|RedirectResponse
    {
        try {
            $aprendiz = $this->aprendizService->obtener($id);

            if (! $aprendiz) {
                return redirect()->route('aprendices.index')
                    ->with('error', 'Aprendiz no encontrado.');
            }

            if (! $aprendiz->persona) {
                return redirect()->route('aprendices.index')
                    ->with('warning', 'Este aprendiz no tiene información de persona asociada.');
            }

            return view('aprendices.show', compact('aprendiz'));
        } catch (Exception $e) {
            Log::error('Error al mostrar aprendiz: '.$e->getMessage());

            return redirect()->route('aprendices.index')
                ->with('error', 'Error al cargar la información del aprendiz.');
        }
    }
}
