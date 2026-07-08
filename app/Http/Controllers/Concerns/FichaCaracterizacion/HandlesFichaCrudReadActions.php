<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesFichaCrudReadActions
{
    public function index(Request $request)
    {
        try {
            $filtros = $request->only(['search', 'estado', 'programa_id', 'jornada_id', 'regional_id']);
            $filtros['per_page'] = 10;

            $fichas = $this->fichaService->listarConFiltros($filtros);
            [
                'programas' => $programas,
                'instructores' => $instructores,
                'ambientes' => $ambientes,
                'sedes' => $sedes,
                'modalidades' => $modalidades,
                'jornadas' => $jornadas,
            ] = $this->loadFichaIndexFilterCatalogs();

            return view('fichas.index', compact('fichas', 'programas', 'instructores', 'ambientes', 'sedes', 'modalidades', 'jornadas'))
                ->with('filters', $request->all());
        } catch (\Exception $e) {
            Log::error('Error al cargar fichas de caracterización', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'Error al cargar las fichas de caracterización. Por favor, intente nuevamente.');
        }
    }

    public function create()
    {
        try {
            Log::info('Acceso al formulario de creación de ficha de caracterización', [
                'user_id' => Auth::id(),
                'timestamp' => now(),
            ]);

            [
                'programas' => $programas,
                'instructores' => $instructores,
                'ambientes' => $ambientes,
                'sedes' => $sedes,
                'modalidades' => $modalidades,
                'jornadas' => $jornadas,
            ] = $this->loadFichaFormCatalogs();

            Log::info('Datos cargados para creación de ficha', [
                'total_programas' => $programas->count(),
                'total_instructores' => $instructores->count(),
                'total_ambientes' => $ambientes->count(),
                'total_sedes' => $sedes->count(),
                'total_modalidades' => $modalidades->count(),
                'total_jornadas' => $jornadas->count(),
                'user_id' => Auth::id(),
            ]);

            return view('fichas.create', compact('programas', 'instructores', 'ambientes', 'sedes', 'modalidades', 'jornadas'));
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de creación de ficha', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'Error al cargar el formulario de creación. Por favor, intente nuevamente.');
        }
    }

    /**
     * Muestra una ficha de caracterización específica.
     *
     * @param  string  $id  El ID de la ficha de caracterización a mostrar.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse La vista que muestra los detalles de la ficha de caracterización.
     */
    public function show(string $id)
    {
        try {
            Log::info('Visualización de ficha de caracterización', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'timestamp' => now(),
            ]);

            $ficha = FichaCaracterizacion::with([
                'programaFormacion',
                'instructor.persona',
                'instructorFicha.instructor.persona',
                'jornadaFormacion.parametro',
                'ambiente',
                'modalidadFormacion',
                'sede',
                'diasFormacion.dia',
                'aprendices',
            ])->findOrFail($id);

            Log::info('Ficha de caracterización cargada para visualización', [
                'ficha_id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'aprendices_count' => $ficha->aprendices->count(),
                'user_id' => Auth::id(),
            ]);

            return view('fichas.show', compact('ficha'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Intento de visualizar ficha de caracterización inexistente', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'La ficha de caracterización solicitada no existe.');

        } catch (\Exception $e) {
            Log::error('Error al cargar ficha de caracterización para visualización', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'Error al cargar los detalles de la ficha. Por favor, intente nuevamente.');
        }
    }
}
