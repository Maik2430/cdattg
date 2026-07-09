<?php

namespace App\Http\Controllers\Concerns\RedConocimiento;

use App\Http\Requests\StoreRedConocimientoRequest;
use App\Http\Requests\UpdateRedConocimientoRequest;
use App\Models\RedConocimiento;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesRedConocimientoCrudWriteActions
{
    /**
     * Almacena una nueva red de conocimiento.
     */
    public function store(StoreRedConocimientoRequest $request)
    {
        try {
            $datos = $request->validated();
            $datos['user_create_id'] = Auth::id();
            $datos['user_edit_id'] = Auth::id();

            $this->redService->crear($datos);

            return redirect()->route('red-conocimiento.index')
                ->with('success', '¡Red de conocimiento creada exitosamente!');
        } catch (QueryException $e) {
            Log::error('Error al crear red de conocimiento: '.$e->getMessage());

            if ($e->getCode() == 23000) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ya existe una red con este nombre.');
            }

            return redirect()->back()->withInput()
                ->with('error', 'Error al crear red de conocimiento.');
        }
    }

    /**
     * Actualiza una red de conocimiento existente.
     */
    public function update(UpdateRedConocimientoRequest $request, RedConocimiento $redConocimiento)
    {
        try {
            $datos = $request->validated();
            $datos['user_edit_id'] = Auth::id();

            $this->redService->actualizar($redConocimiento->id, $datos);

            return redirect()->route('red-conocimiento.show', $redConocimiento->id)
                ->with('success', 'Red de conocimiento actualizada exitosamente.');
        } catch (QueryException $e) {
            Log::error('Error al actualizar red de conocimiento: '.$e->getMessage());

            if ($e->getCode() == 23000) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ya existe una red con este nombre.');
            }

            return redirect()->back()->withInput()
                ->with('error', 'Error al actualizar red de conocimiento.');
        }
    }

    /**
     * Elimina una red de conocimiento.
     */
    public function destroy(RedConocimiento $redConocimiento)
    {
        try {
            $this->redService->eliminar($redConocimiento->id);

            return redirect()->route('red-conocimiento.index')
                ->with('success', 'Red de conocimiento eliminada exitosamente.');
        } catch (QueryException $e) {
            Log::error('Error al eliminar red de conocimiento: '.$e->getMessage());

            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->with('error', 'La red está en uso, no se puede eliminar.');
            }

            return redirect()->back()->with('error', 'Error al eliminar red.');
        }
    }
}
