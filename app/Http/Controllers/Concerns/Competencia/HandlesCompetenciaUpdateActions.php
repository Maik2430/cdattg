<?php

namespace App\Http\Controllers\Concerns\Competencia;

use App\Http\Requests\UpdateCompetenciaRequest;
use App\Models\Competencia;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesCompetenciaUpdateActions
{
    use HandlesCompetenciaDuracionHelpers;
    use HandlesCompetenciaResultadosUpdateHelpers;

    public function update(UpdateCompetenciaRequest $request, Competencia $competencia): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $duracionAnterior = $competencia->duracion;

            $data = $request->validated();
            $data['user_edit_id'] = Auth::id();

            $competencia->update($data);

            $resultadosData = $request->input('resultados', []);
            $procesamiento = $this->processCompetenciaResultadosFromRequest($competencia, $resultadosData);

            if (isset($data['duracion']) && $data['duracion'] != $duracionAnterior) {
                $this->redistribuirDuracionResultados($competencia);
            }

            DB::commit();

            Log::info('Competencia actualizada exitosamente', [
                'competencia_id' => $competencia->id,
                'codigo' => $competencia->codigo,
                'duracion_anterior' => $duracionAnterior,
                'duracion_nueva' => $competencia->duracion,
                'resultados_procesados' => count($procesamiento['resultados_ids']),
                'resultados_eliminados' => count($procesamiento['resultados_a_eliminar']),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('competencias.index')
                ->with('success', "Competencia '{$competencia->codigo}' actualizada exitosamente.");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar competencia: '.$e->getMessage(), [
                'competencia_id' => $competencia->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la competencia. Intente nuevamente.');
        }
    }
}
