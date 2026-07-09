<?php

namespace App\Http\Controllers\Concerns\GuiaAprendizaje;

use App\Http\Requests\StoreGuiasAprendizajeRequest;
use App\Http\Requests\UpdateGuiasAprendizajeRequest;
use App\Models\GuiasAprendizaje;
use App\Models\ResultadosAprendizaje;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesGuiaAprendizajeCrudWriteActions
{
    public function create()
    {
        try {
            $resultadosAprendizaje = ResultadosAprendizaje::whereRaw('status = 1')
                ->orderBy('codigo')
                ->get();

            if ($resultadosAprendizaje->isEmpty()) {
                $resultadosAprendizaje = ResultadosAprendizaje::orderBy('codigo')->get();
                Log::warning('No hay resultados activos al crear guía; se listan todos', [
                    'total' => $resultadosAprendizaje->count(),
                    'user_id' => Auth::id(),
                ]);
            }

            return view('guias_aprendizaje.create', compact('resultadosAprendizaje'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de creación de guía: '.$e->getMessage(), [
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Error al cargar el formulario de creación.');
        }
    }

    public function store(StoreGuiasAprendizajeRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_create_id'] = Auth::id();
            $data['user_edit_id'] = Auth::id();

            $guiaAprendizaje = GuiasAprendizaje::create($data);

            if ($request->has('resultados_aprendizaje')) {
                $guiaAprendizaje->resultadosAprendizaje()->sync($request->resultados_aprendizaje);
            }

            DB::commit();

            return redirect()->route('guias-aprendizaje.index')
                ->with('success', 'Guía de aprendizaje creada exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear guía de aprendizaje: '.$e->getMessage(), [
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la guía de aprendizaje. Intente nuevamente.');
        }
    }

    public function edit(GuiasAprendizaje $guiaAprendizaje)
    {
        try {
            $resultadosAprendizaje = ResultadosAprendizaje::orderBy('codigo')->get();
            $guiaAprendizaje->load('resultadosAprendizaje');

            return view('guias_aprendizaje.edit', compact('guiaAprendizaje', 'resultadosAprendizaje'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición de guía: '.$e->getMessage(), [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Error al cargar el formulario de edición.');
        }
    }

    public function update(UpdateGuiasAprendizajeRequest $request, GuiasAprendizaje $guiaAprendizaje): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_edit_id'] = Auth::id();
            $guiaAprendizaje->update($data);

            if ($request->has('resultados_aprendizaje')) {
                $guiaAprendizaje->resultadosAprendizaje()->sync($request->resultados_aprendizaje);
            }

            DB::commit();

            return redirect()->route('guias-aprendizaje.index')
                ->with('success', 'Guía de aprendizaje actualizada exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar guía de aprendizaje: '.$e->getMessage(), [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la guía de aprendizaje. Intente nuevamente.');
        }
    }

    public function destroy(GuiasAprendizaje $guiaAprendizaje): RedirectResponse
    {
        try {
            if ($guiaAprendizaje->actividades()->exists()) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar la guía de aprendizaje porque tiene actividades asociadas.');
            }

            DB::beginTransaction();
            $guiaAprendizaje->resultadosAprendizaje()->detach();
            $guiaAprendizaje->delete();
            DB::commit();

            return redirect()->route('guias-aprendizaje.index')
                ->with('success', 'Guía de aprendizaje eliminada exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar guía de aprendizaje: '.$e->getMessage(), [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('error', 'Error al eliminar la guía de aprendizaje. Intente nuevamente.');
        }
    }
}
