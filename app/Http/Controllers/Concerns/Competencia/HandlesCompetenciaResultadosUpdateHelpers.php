<?php

namespace App\Http\Controllers\Concerns\Competencia;

use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait HandlesCompetenciaResultadosUpdateHelpers
{
    /**
     * @param  array<int, array<string, mixed>>  $resultadosData
     * @return array{resultados_ids: array<int, int>, resultados_a_eliminar: array<int, int>}
     */
    protected function processCompetenciaResultadosFromRequest(Competencia $competencia, array $resultadosData): array
    {
        $resultadosIds = [];

        foreach ($resultadosData as $resultadoData) {
            $resultadoId = $resultadoData['id'] ?? null;
            $codigo = $resultadoData['codigo'] ?? '';
            $nombre = $resultadoData['nombre'] ?? '';
            $horas = (float) ($resultadoData['horas'] ?? 0);

            if (empty($codigo) || empty($nombre)) {
                continue;
            }

            if ($resultadoId) {
                $resultado = ResultadosAprendizaje::find($resultadoId);
                if ($resultado) {
                    $resultado->update([
                        'codigo' => $codigo,
                        'nombre' => $nombre,
                        'duracion' => $horas,
                        'user_edit_id' => Auth::id(),
                    ]);

                    DB::table('resultados_aprendizaje_competencia')
                        ->where('competencia_id', $competencia->id)
                        ->where('rap_id', $resultadoId)
                        ->update([
                            'duracion' => $horas,
                            'updated_at' => now(),
                        ]);

                    $resultadosIds[] = $resultadoId;
                }
            } else {
                $resultado = ResultadosAprendizaje::create([
                    'codigo' => $codigo,
                    'nombre' => $nombre,
                    'duracion' => $horas,
                    'status' => 1,
                    'user_create_id' => Auth::id(),
                    'user_edit_id' => Auth::id(),
                ]);

                $competencia->resultadosAprendizaje()->attach($resultado->id, [
                    'duracion' => $horas,
                    'user_create_id' => Auth::id(),
                    'user_edit_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $resultadosIds[] = $resultado->id;
            }
        }

        $resultadosActuales = $competencia->resultadosAprendizaje()->pluck('resultados_aprendizajes.id')->toArray();
        $resultadosAEliminar = array_diff($resultadosActuales, $resultadosIds);

        foreach ($resultadosAEliminar as $resultadoIdEliminar) {
            $competencia->resultadosAprendizaje()->detach($resultadoIdEliminar);
        }

        return [
            'resultados_ids' => $resultadosIds,
            'resultados_a_eliminar' => $resultadosAEliminar,
        ];
    }
}
