<?php

namespace App\Http\Controllers\Concerns\Aprendiz;

use App\Http\Requests\StoreAprendizRequest;
use App\Http\Requests\UpdateAprendizRequest;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

trait HandlesAprendizCrudWriteActions
{
    use HandlesAprendizFormDataHelpers;

    /**
     * Muestra el formulario para crear un nuevo aprendiz.
     */
    public function create(): View|RedirectResponse
    {
        try {
            $personas = $this->getAprendizPersonasDisponibles();
            $fichas = $this->getAprendizFichasActivas();

            return view('aprendices.create', compact('personas', 'fichas'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de creación de aprendiz: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cargar el formulario.');
        }
    }

    /**
     * Almacena un nuevo aprendiz en la base de datos.
     */
    public function store(StoreAprendizRequest $request): RedirectResponse
    {
        try {
            $this->aprendizService->crear($request->validated());

            return redirect()->route('aprendices.index')
                ->with('success', '¡Aprendiz registrado exitosamente!');
        } catch (Exception $e) {
            Log::error('Error al crear aprendiz', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar un aprendiz.
     *
     * @param  int  $id
     */
    public function edit($id): View|RedirectResponse
    {
        try {
            $aprendiz = $this->aprendizService->obtener($id);

            if (! $aprendiz) {
                return redirect()->route('aprendices.index')
                    ->with('error', 'Aprendiz no encontrado.');
            }

            $fichas = $this->getAprendizFichasActivas();

            return view('aprendices.edit', compact('aprendiz', 'fichas'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición: '.$e->getMessage());

            return redirect()->route('aprendices.index')
                ->with('error', 'Error al cargar el formulario.');
        }
    }

    /**
     * Actualiza la información de un aprendiz en la base de datos.
     *
     * @param  int  $id
     */
    public function update(UpdateAprendizRequest $request, $id): RedirectResponse
    {
        try {
            $this->aprendizService->actualizar($id, $request->validated());

            return redirect()->route('aprendices.show', $id)
                ->with('success', 'Información del aprendiz actualizada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar aprendiz: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Elimina un aprendiz de la base de datos.
     *
     * @param  int  $id
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $this->aprendizService->eliminar($id);

            return redirect()->route('aprendices.index')
                ->with('success', 'Aprendiz eliminado exitosamente.');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al eliminar aprendiz: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'No se puede eliminar el aprendiz porque tiene registros asociados.');
        } catch (Exception $e) {
            Log::error('Error al eliminar aprendiz: '.$e->getMessage());

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
