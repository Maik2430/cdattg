<?php

namespace App\Http\Controllers\Concerns\Tema;

use App\Http\Requests\StoreTemaRequest;
use App\Http\Requests\UpdateTemaRequest;
use App\Models\Tema;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesTemaCrudWriteActions
{
    public function store(StoreTemaRequest $request)
    {
        try {
            $datos = $request->validated();
            $datos['user_create_id'] = Auth::id();
            $datos['user_edit_id'] = Auth::id();

            $this->temaService->crear($datos);

            return redirect()->back()->with('success', '¡Tema creado exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al crear tema: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Error al crear el tema.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTemaRequest $request, Tema $tema)
    {
        try {
            $datos = $request->validated();
            $datos['user_edit_id'] = Auth::id();

            $this->temaService->actualizar($tema->id, $datos);

            return redirect()->route('tema.show', $tema->id)->with('success', 'Tema actualizado exitosamente');
        } catch (QueryException $e) {
            Log::error('Error al actualizar tema: '.$e->getMessage());

            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'El nombre del tema ya existe.');
            }

            return redirect()->back()->with('error', 'Error al actualizar el tema.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tema $tema)
    {
        try {
            $this->temaService->eliminar($tema->id);

            return redirect()->route('tema.index')->with('success', 'Tema eliminado exitosamente');
        } catch (QueryException $e) {
            Log::error('Error al eliminar tema: '.$e->getMessage());

            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'El tema está en uso, no se puede eliminar');
            }

            return redirect()->back()->with('error', 'Error al eliminar tema.');
        }
    }
}
