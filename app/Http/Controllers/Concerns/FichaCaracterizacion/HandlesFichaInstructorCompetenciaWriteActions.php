<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaInstructorCompetenciaWriteActions
{
    /**
     * Actualiza las competencias y resultados de aprendizaje de un instructor asignado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizarCompetenciasInstructor(Request $request, string $fichaId, string $instructorFichaId)
    {
        try {
            $validated = $request->validate([
                'competencia_id' => 'nullable|integer|exists:competencias,id',
                'resultados_aprendizaje' => 'nullable|array',
                'resultados_aprendizaje.*' => 'required|integer|exists:resultados_aprendizajes,id',
            ]);

            $instructorFicha = \App\Models\InstructorFichaCaracterizacion::with(['ficha.programaFormacion', 'competencia', 'resultadosAprendizaje'])
                ->findOrFail($instructorFichaId);

            // Verificar que el instructor pertenezca a la ficha
            if ($instructorFicha->ficha_id != $fichaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'El instructor no pertenece a esta ficha',
                ], 422);
            }

            // Validar que la competencia pertenezca al programa de formación
            if ($validated['competencia_id']) {
                $ficha = $instructorFicha->ficha;
                if (! $ficha->programaFormacion) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La ficha no tiene un programa de formación asociado.',
                    ], 422);
                }

                $competenciaPertenece = $ficha->programaFormacion->competencias->contains('id', $validated['competencia_id']);
                if (! $competenciaPertenece) {
                    $competencia = \App\Models\Competencia::find($validated['competencia_id']);
                    $competenciaNombre = $competencia ? $competencia->nombre : 'Competencia desconocida';

                    return response()->json([
                        'success' => false,
                        'message' => "La competencia '{$competenciaNombre}' no pertenece al programa de formación de esta ficha.",
                    ], 422);
                }

                // Validar que los resultados pertenezcan a la competencia
                if (! empty($validated['resultados_aprendizaje'])) {
                    $competencia = \App\Models\Competencia::with('resultadosAprendizaje')->find($validated['competencia_id']);
                    if ($competencia) {
                        foreach ($validated['resultados_aprendizaje'] as $resultadoId) {
                            $resultadoPertenece = $competencia->resultadosAprendizaje->contains('id', $resultadoId);
                            if (! $resultadoPertenece) {
                                $resultado = \App\Models\ResultadosAprendizaje::find($resultadoId);
                                $resultadoNombre = $resultado ? $resultado->nombre : 'Resultado desconocido';

                                return response()->json([
                                    'success' => false,
                                    'message' => "El resultado de aprendizaje '{$resultadoNombre}' no pertenece a la competencia seleccionada.",
                                ], 422);
                            }
                        }
                    }
                }
            }

            DB::beginTransaction();

            // Actualizar competencia
            $instructorFicha->competencia_id = $validated['competencia_id'] ?? null;
            $instructorFicha->save();

            // Sincronizar resultados de aprendizaje
            if (! empty($validated['resultados_aprendizaje'])) {
                $instructorFicha->resultadosAprendizaje()->sync($validated['resultados_aprendizaje']);
            } else {
                $instructorFicha->resultadosAprendizaje()->detach();
            }

            DB::commit();

            Log::info('Competencias y resultados actualizados exitosamente', [
                'instructor_ficha_id' => $instructorFichaId,
                'competencia_id' => $validated['competencia_id'] ?? null,
                'resultados_count' => count($validated['resultados_aprendizaje'] ?? []),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Competencias y resultados de aprendizaje actualizados correctamente',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar competencias y resultados', [
                'ficha_id' => $fichaId,
                'instructor_ficha_id' => $instructorFichaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar competencias y resultados: '.$e->getMessage(),
            ], 500);
        }
    }
}
