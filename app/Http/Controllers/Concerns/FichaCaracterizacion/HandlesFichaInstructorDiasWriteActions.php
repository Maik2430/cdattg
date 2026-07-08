<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesFichaInstructorDiasWriteActions
{
    /**
     * Asigna días de formación a un instructor en una ficha.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function asignarDiasInstructor(Request $request, string $fichaId, string $instructorFichaId)
    {
        try {
            // Log de datos recibidos para debug
            Log::info('Datos recibidos para asignar días', [
                'ficha_id' => $fichaId,
                'instructor_ficha_id' => $instructorFichaId,
                'request_data' => $request->all(),
            ]);

            $validated = $request->validate([
                'dias' => 'required|array|min:1',
                'dias.*.dia_id' => 'required|integer|exists:parametros_temas,id',
                'dias.*.hora_inicio' => 'nullable|date_format:H:i',
                'dias.*.hora_fin' => 'nullable|date_format:H:i',
            ]);

            Log::info('Datos validados', ['validated' => $validated]);

            // Verificar que los IDs de días existen
            foreach ($validated['dias'] as $dia) {
                $parametro = \App\Models\Parametro::find($dia['dia_id']);
                Log::info("Verificando día ID {$dia['dia_id']}", [
                    'existe' => $parametro ? 'SÍ' : 'NO',
                    'nombre' => $parametro ? $parametro->name : 'N/A',
                ]);
            }

            $diasService = app(\App\Services\InstructorFichaDiasService::class);
            $resultado = $diasService->asignarDiasInstructor($instructorFichaId, $validated['dias']);

            Log::info('Resultado del servicio', ['resultado' => $resultado]);

            return response()->json($resultado);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación al asignar días', [
                'ficha_id' => $fichaId,
                'instructor_ficha_id' => $instructorFichaId,
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al asignar días de instructor', [
                'ficha_id' => $fichaId,
                'instructor_ficha_id' => $instructorFichaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al asignar días de formación',
            ], 500);
        }
    }

    /**
     * Elimina los días asignados a un instructor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function eliminarDiasInstructor(string $fichaId, string $instructorFichaId)
    {
        try {
            \App\Models\InstructorFichaDias::where('instructor_ficha_id', $instructorFichaId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Días de formación eliminados correctamente',
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar días de instructor', [
                'ficha_id' => $fichaId,
                'instructor_ficha_id' => $instructorFichaId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar días de formación',
            ], 500);
        }
    }
}
