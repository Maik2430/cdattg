<?php

namespace App\Http\Controllers\Concerns\ResultadosAprendizaje;

use App\Http\Controllers\Concerns\Competencia\HandlesCompetenciaDuracionHelpers;
use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesResultadosAprendizajeCompetenciasActions
{
    use HandlesCompetenciaDuracionHelpers;

    public function cambiarEstado(ResultadosAprendizaje $resultadoAprendizaje): RedirectResponse
    {
        try {
            $nuevoEstado = $resultadoAprendizaje->status === 1 ? 0 : 1;
            $resultadoAprendizaje->update([
                'status' => $nuevoEstado,
                'user_edit_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Estado cambiado exitosamente');
        } catch (Exception $e) {
            Log::error('Error al cambiar estado de resultado: '.$e->getMessage(), [
                'resultado_id' => $resultadoAprendizaje->id,
            ]);

            return redirect()->back()->with('error', 'Error al cambiar el estado. Intente nuevamente.');
        }
    }

    public function gestionarCompetencias(ResultadosAprendizaje $resultadoAprendizaje)
    {
        try {
            $competenciasAsignadas = $resultadoAprendizaje->competencias()->get();
            $competenciasDisponibles = Competencia::whereNotIn('id', $competenciasAsignadas->pluck('id'))
                ->orderBy('nombre')
                ->get();

            return view('resultados_aprendizaje.gestionar_competencias', compact(
                'resultadoAprendizaje',
                'competenciasAsignadas',
                'competenciasDisponibles'
            ));
        } catch (Exception $e) {
            Log::error('Error al gestionar competencias de resultado: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cargar la gestión de competencias.');
        }
    }

    public function asociarCompetencia(Request $request, ResultadosAprendizaje $resultadoAprendizaje): RedirectResponse
    {
        try {
            $request->validate(['competencia_id' => 'required|exists:competencias,id']);

            $competenciaId = (int) $request->competencia_id;

            if ($resultadoAprendizaje->competencias()->where('competencias.id', $competenciaId)->exists()) {
                return redirect()->back()->with('error', 'Esta competencia ya está asignada al resultado.');
            }

            $resultadoAprendizaje->competencias()->attach($competenciaId, [
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Competencia asociada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al asociar competencia: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al asociar la competencia.');
        }
    }

    public function desasociarCompetencia(ResultadosAprendizaje $resultadoAprendizaje, Competencia $competencia): RedirectResponse
    {
        try {
            if (! $resultadoAprendizaje->competencias()->where('competencias.id', $competencia->id)->exists()) {
                return redirect()->back()->with('error', 'Esta competencia no está asignada al resultado.');
            }

            $resultadoAprendizaje->competencias()->detach($competencia->id);
            $this->redistribuirDuracionResultados($competencia);

            return redirect()->back()->with('success', 'Competencia desasociada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al desasociar competencia: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al desasociar la competencia.');
        }
    }
}
