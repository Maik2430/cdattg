<?php

namespace App\Http\Controllers\Concerns\Competencia;

use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;
use Illuminate\Support\Facades\Auth;

trait HandlesCompetenciaResultadosAssociationHelpers
{
    use HandlesCompetenciaDuracionHelpers;

    protected function attachResultadoToCompetencia(
        Competencia $competencia,
        int $resultadoId,
        bool $updateResultadoModel = false
    ): ResultadosAprendizaje {
        $resultado = ResultadosAprendizaje::findOrFail($resultadoId);

        $totalResultados = $competencia->resultadosAprendizaje()->count() + 1;
        $duracionPorResultado = $totalResultados > 0 ? $competencia->duracion / $totalResultados : 0;

        $this->redistribuirDuracionResultados($competencia);

        $competencia->resultadosAprendizaje()->attach($resultadoId, [
            'duracion' => $duracionPorResultado,
            'user_create_id' => Auth::id(),
            'user_edit_id' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($updateResultadoModel) {
            $resultado->update([
                'duracion' => $duracionPorResultado,
            ]);
        }

        $this->redistribuirDuracionResultados($competencia);

        return $resultado;
    }

    /**
     * @return array{error: string|null, resultado: ResultadosAprendizaje|null}
     */
    protected function validateResultadoForAssociation(Competencia $competencia, int $resultadoId): array
    {
        $resultado = ResultadosAprendizaje::findOrFail($resultadoId);

        if (! $resultado->status) {
            return [
                'error' => 'No se puede asociar un Resultado de Aprendizaje inactivo.',
                'resultado' => null,
            ];
        }

        if (! $competencia->status) {
            return [
                'error' => 'No se pueden asociar resultados a una competencia inactiva.',
                'resultado' => null,
            ];
        }

        if ($competencia->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultadoId)->exists()) {
            return [
                'error' => 'Este resultado de aprendizaje ya está asignado a la competencia.',
                'resultado' => null,
            ];
        }

        return [
            'error' => null,
            'resultado' => $resultado,
        ];
    }
}
