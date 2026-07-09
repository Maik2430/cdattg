<?php

namespace App\Http\Controllers\Concerns\ResultadosAprendizaje;

use App\Http\Controllers\Concerns\Competencia\HandlesCompetenciaDuracionHelpers;
use App\Http\Requests\StoreResultadosAprendizajeRequest;
use App\Http\Requests\UpdateResultadosAprendizajeRequest;
use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesResultadosAprendizajeCrudWriteActions
{
    use HandlesCompetenciaDuracionHelpers;

    public function create()
    {
        try {
            $competencias = Competencia::orderBy('nombre')->get();

            return view('resultados_aprendizaje.create', compact('competencias'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de creación de resultado: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cargar el formulario de creación.');
        }
    }

    public function store(StoreResultadosAprendizajeRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_create_id'] = Auth::id();
            $data['user_edit_id'] = Auth::id();

            $resultadoAprendizaje = ResultadosAprendizaje::create($data);

            if ($request->filled('competencia_id')) {
                $this->asociarResultadoACompetencia($resultadoAprendizaje, (int) $request->competencia_id);
            }

            DB::commit();

            return redirect()->route('resultados-aprendizaje.index')
                ->with('success', 'Resultado de aprendizaje creado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear resultado de aprendizaje: '.$e->getMessage(), [
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el resultado de aprendizaje. Intente nuevamente.');
        }
    }

    public function edit(ResultadosAprendizaje $resultadoAprendizaje)
    {
        try {
            $competencias = Competencia::orderBy('nombre')->get();
            $resultadoAprendizaje->load('competencias');

            return view('resultados_aprendizaje.edit', compact('resultadoAprendizaje', 'competencias'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición de resultado: '.$e->getMessage(), [
                'resultado_id' => $resultadoAprendizaje->id,
            ]);

            return redirect()->back()->with('error', 'Error al cargar el formulario de edición.');
        }
    }

    public function update(UpdateResultadosAprendizajeRequest $request, ResultadosAprendizaje $resultadoAprendizaje): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_edit_id'] = Auth::id();
            $resultadoAprendizaje->update($data);

            if ($request->has('competencia_id')) {
                $syncData = [];
                if ($request->competencia_id) {
                    $syncData[$request->competencia_id] = ['user_edit_id' => Auth::id()];
                }
                $resultadoAprendizaje->competencias()->sync($syncData);
            }

            DB::commit();

            return redirect()->route('resultados-aprendizaje.index')
                ->with('success', 'Resultado de aprendizaje actualizado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar resultado de aprendizaje: '.$e->getMessage(), [
                'resultado_id' => $resultadoAprendizaje->id,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el resultado de aprendizaje. Intente nuevamente.');
        }
    }

    public function destroy(ResultadosAprendizaje $resultadoAprendizaje): RedirectResponse
    {
        try {
            $cantidadGuias = $resultadoAprendizaje->guiasAprendizaje()->count();
            if ($cantidadGuias > 0) {
                return redirect()->back()
                    ->with('error', "No se puede eliminar el resultado de aprendizaje '{$resultadoAprendizaje->codigo}' porque tiene {$cantidadGuias} guía(s) asociada(s).");
            }

            DB::beginTransaction();

            $codigoResultado = $resultadoAprendizaje->codigo;
            $competenciasAsociadas = $resultadoAprendizaje->competencias()->get();
            $resultadoAprendizaje->competencias()->detach();

            foreach ($competenciasAsociadas as $competencia) {
                $this->redistribuirDuracionResultados($competencia);
            }

            $resultadoAprendizaje->delete();
            DB::commit();

            return redirect()->route('resultados-aprendizaje.index')
                ->with('success', "Resultado de aprendizaje '{$codigoResultado}' eliminado exitosamente.");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar resultado de aprendizaje: '.$e->getMessage(), [
                'resultado_id' => $resultadoAprendizaje->id,
            ]);

            return redirect()->back()
                ->with('error', 'Error al eliminar el resultado de aprendizaje. Intente nuevamente.');
        }
    }

    private function asociarResultadoACompetencia(ResultadosAprendizaje $resultado, int $competenciaId): void
    {
        $competencia = Competencia::findOrFail($competenciaId);
        $this->redistribuirDuracionResultados($competencia);

        $totalResultados = $competencia->resultadosAprendizaje()->count() + 1;
        $duracionPorResultado = $totalResultados > 0 ? $competencia->duracion / $totalResultados : 0;

        $resultado->competencias()->attach($competenciaId, [
            'duracion' => $duracionPorResultado,
            'user_create_id' => Auth::id(),
            'user_edit_id' => Auth::id(),
        ]);

        $resultado->update(['duracion' => $duracionPorResultado]);
        $this->redistribuirDuracionResultados($competencia);
    }
}
