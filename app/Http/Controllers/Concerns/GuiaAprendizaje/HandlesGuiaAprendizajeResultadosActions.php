<?php

namespace App\Http\Controllers\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\ResultadosAprendizaje;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesGuiaAprendizajeResultadosActions
{
    public function gestionarResultados(GuiasAprendizaje $guiaAprendizaje)
    {
        try {
            $resultadosAsignados = $guiaAprendizaje->resultadosAprendizaje()
                ->with(['competencias'])
                ->get();

            $resultadosDisponibles = ResultadosAprendizaje::whereNotIn('id', $resultadosAsignados->pluck('id'))
                ->with(['competencias'])
                ->orderBy('codigo')
                ->get();

            $resultadosPorCompetencia = $resultadosDisponibles->groupBy(function ($resultado) {
                return $resultado->competencias->first()->nombre ?? 'Sin Competencia';
            });

            return view('guias_aprendizaje.gestionar_resultados', compact(
                'guiaAprendizaje',
                'resultadosAsignados',
                'resultadosDisponibles',
                'resultadosPorCompetencia'
            ));
        } catch (Exception $e) {
            Log::error('Error al gestionar resultados de guía: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cargar la gestión de resultados.');
        }
    }

    public function asociarResultado(Request $request, GuiasAprendizaje $guiaAprendizaje): RedirectResponse
    {
        try {
            $request->validate([
                'resultado_id' => 'required|exists:resultados_aprendizajes,id',
                'es_obligatorio' => 'boolean',
            ]);

            $resultadoId = (int) $request->resultado_id;
            $esObligatorio = $request->boolean('es_obligatorio', true);

            if ($guiaAprendizaje->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultadoId)->exists()) {
                return redirect()->back()->with('error', 'Este resultado ya está asignado a la guía.');
            }

            if ($this->guiaService->resultadosTienenCompetenciasDistintas($guiaAprendizaje, $resultadoId)) {
                return redirect()->back()->with('error', 'Los resultados deben pertenecer a la misma competencia.');
            }

            $guiaAprendizaje->resultadosAprendizaje()->attach($resultadoId, [
                'es_obligatorio' => $esObligatorio,
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Resultado asociado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al asociar resultado: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al asociar el resultado.');
        }
    }

    public function desasociarResultado(GuiasAprendizaje $guiaAprendizaje, ResultadosAprendizaje $resultado): RedirectResponse
    {
        try {
            if (! $guiaAprendizaje->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultado->id)->exists()) {
                return redirect()->back()->with('error', 'Este resultado no está asignado a la guía.');
            }

            $guiaAprendizaje->resultadosAprendizaje()->detach($resultado->id);

            return redirect()->back()->with('success', 'Resultado desasociado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al desasociar resultado: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al desasociar el resultado.');
        }
    }

    public function cambiarObligatoriedad(
        Request $request,
        GuiasAprendizaje $guiaAprendizaje,
        ResultadosAprendizaje $resultado
    ): RedirectResponse {
        try {
            $request->validate(['es_obligatorio' => 'required|boolean']);

            $esObligatorio = $request->boolean('es_obligatorio');

            $guiaAprendizaje->resultadosAprendizaje()->updateExistingPivot($resultado->id, [
                'es_obligatorio' => $esObligatorio,
                'user_edit_id' => Auth::id(),
                'updated_at' => now(),
            ]);

            $mensaje = $esObligatorio
                ? 'Resultado marcado como obligatorio'
                : 'Resultado marcado como opcional';

            return redirect()->back()->with('success', $mensaje);
        } catch (Exception $e) {
            Log::error('Error al cambiar obligatoriedad: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cambiar la obligatoriedad del resultado.');
        }
    }
}
