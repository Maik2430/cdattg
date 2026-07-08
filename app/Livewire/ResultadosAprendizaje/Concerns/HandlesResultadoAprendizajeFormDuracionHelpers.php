<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\Competencia;

trait HandlesResultadoAprendizajeFormDuracionHelpers
{
    private function redistribuirDuracionCompetencia(Competencia $competencia): void
    {
        $resultados = $competencia->resultadosAprendizaje()->get();
        $totalResultados = $resultados->count();

        if ($totalResultados === 0) {
            return;
        }

        $duracionPorResultado = $competencia->duracion / $totalResultados;

        foreach ($resultados as $resultado) {
            \DB::table('resultados_aprendizaje_competencia')
                ->where('competencia_id', $competencia->id)
                ->where('rap_id', $resultado->id)
                ->update([
                    'duracion' => $duracionPorResultado,
                    'updated_at' => now(),
                ]);

            $resultado->update([
                'duracion' => $duracionPorResultado,
            ]);
        }
    }
}
