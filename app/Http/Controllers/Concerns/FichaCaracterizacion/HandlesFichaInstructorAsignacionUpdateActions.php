<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaInstructorAsignacionUpdateActions
{
    public function actualizarAsignacionInstructor(Request $request, string $fichaId, string $instructorFichaId)
    {
        try {
            $instructorFicha = \App\Models\InstructorFichaCaracterizacion::with(['instructor', 'ficha'])->findOrFail($instructorFichaId);

            if ($instructorFicha->ficha_id != $fichaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'El instructor no pertenece a esta ficha',
                ], 422);
            }

            $instructorData = $this->prepareInstructorDataForAsignacionUpdate($request, $instructorFicha);
            $validator = $this->validateActualizarAsignacionInstructor($instructorData, $fichaId);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $instructorFicha->fecha_inicio = $instructorData['fecha_inicio'];
            $instructorFicha->fecha_fin = $instructorData['fecha_fin'];
            $instructorFicha->competencia_id = $instructorData['competencia_id'] ?? null;
            $instructorFicha->save();

            if (! empty($instructorData['resultados_aprendizaje'])) {
                $instructorFicha->resultadosAprendizaje()->sync($instructorData['resultados_aprendizaje']);
            } else {
                $instructorFicha->resultadosAprendizaje()->detach();
            }

            $diasService = app(\App\Services\InstructorFichaDiasService::class);
            $diasParaServicio = [];
            foreach ($instructorData['dias'] as $diaId => $diaInfo) {
                $diasParaServicio[] = [
                    'dia_id' => $diaId,
                    'hora_inicio' => $diaInfo['hora_inicio'] ?? null,
                    'hora_fin' => $diaInfo['hora_fin'] ?? null,
                ];
            }

            $resultadoDias = $diasService->asignarDiasInstructor($instructorFichaId, $diasParaServicio);

            if (! $resultadoDias['success']) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => $resultadoDias['message'],
                    'conflictos' => $resultadoDias['conflictos'] ?? [],
                ], 422);
            }

            $instructorFicha->refresh();

            DB::commit();

            Log::info('Asignación de instructor actualizada exitosamente', [
                'instructor_ficha_id' => $instructorFichaId,
                'ficha_id' => $fichaId,
                'instructor_id' => $instructorFicha->instructor_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Asignación actualizada correctamente con todas las validaciones aplicadas',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar asignación de instructor', [
                'ficha_id' => $fichaId,
                'instructor_ficha_id' => $instructorFichaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la asignación: '.$e->getMessage(),
            ], 500);
        }
    }
}
