<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use Illuminate\Support\Facades\Log;

trait HandlesFichaInstructorCompetenciaReadActions
{
    /**
     * Obtiene la competencia y resultados asignados a un instructor para edición.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerCompetenciaYResultadosInstructor(string $fichaId, string $instructorFichaId)
    {
        try {
            $instructorFicha = \App\Models\InstructorFichaCaracterizacion::with(['competencia', 'resultadosAprendizaje'])
                ->findOrFail($instructorFichaId);

            // Verificar que pertenezca a la ficha
            if ($instructorFicha->ficha_id != $fichaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'El instructor no pertenece a esta ficha',
                ], 422);
            }

            $competencia = null;
            if ($instructorFicha->competencia) {
                $competencia = [
                    'id' => $instructorFicha->competencia->id,
                    'codigo' => $instructorFicha->competencia->codigo,
                    'nombre' => $instructorFicha->competencia->nombre,
                ];
            }

            $resultados = $instructorFicha->resultadosAprendizaje->map(function ($resultado) {
                return [
                    'id' => $resultado->id,
                    'codigo' => $resultado->codigo,
                    'nombre' => $resultado->nombre,
                    'duracion' => $resultado->duracion ?? 0,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'competencia' => $competencia,
                    'resultados' => $resultados,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener competencia y resultados del instructor', [
                'ficha_id' => $fichaId,
                'instructor_ficha_id' => $instructorFichaId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos del instructor',
            ], 500);
        }
    }
}
