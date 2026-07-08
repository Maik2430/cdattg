<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Models\Aprendiz;
use App\Models\Asistencia;
use App\Models\AsistenciaAprendiz;
use App\Models\Persona;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistenceBulkRegistration
{
    public function registrarAsistenciaSeleccionados(Request $request)
    {
        try {
            $request->validate([
                'ficha_id' => 'required|integer',
                'caracterizacion_id' => 'required|integer',
                'asistencia_id' => 'required|integer',
                'aprendices_seleccionados' => 'nullable|array',
                'documento_manual' => 'nullable|string|max:20',
            ]);

            $fichaId = $request->input('ficha_id');
            $asistenciaId = $request->input('asistencia_id');
            $aprendicesSeleccionados = $request->input('aprendices_seleccionados', []);
            $documentoManual = $request->input('documento_manual');

            $asistencia = Asistencia::find($asistenciaId);
            if (! $asistencia) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesión de asistencia no encontrada.',
                ], 404);
            }

            $registrosExitosos = 0;
            $errores = [];
            $aprendicesActualizados = [];

            foreach ($aprendicesSeleccionados as $aprendizData) {
                $resultado = $this->procesarAprendizSeleccionado(
                    $aprendizData,
                    $asistencia,
                    $asistenciaId
                );
                $registrosExitosos += $resultado['exitosos'];
                $errores = array_merge($errores, $resultado['errores']);
                $aprendicesActualizados = array_merge($aprendicesActualizados, $resultado['actualizados']);
            }

            if ($documentoManual) {
                $resultadoManual = $this->procesarDocumentoManual(
                    $documentoManual,
                    $fichaId,
                    $asistencia,
                    $asistenciaId
                );
                $registrosExitosos += $resultadoManual['exitosos'];
                $errores = array_merge($errores, $resultadoManual['errores']);
            }

            $message = "Se registraron {$registrosExitosos} asistencias correctamente.";
            if (! empty($errores)) {
                $message .= ' Errores: '.implode(', ', $errores);
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'registros_exitosos' => $registrosExitosos,
                'errores' => $errores,
                'aprendices_actualizados' => $aprendicesActualizados,
            ]);
        } catch (Exception $e) {
            Log::error('Error general en registrarAsistenciaSeleccionados: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la solicitud: '.$e->getMessage(),
            ], 500);
        }
    }

    /** @return array{exitosos: int, errores: list<string>, actualizados: list<array<string, mixed>>} */
    private function procesarAprendizSeleccionado(array $aprendizData, Asistencia $asistencia, int $asistenciaId): array
    {
        $aprendizId = $aprendizData['aprendiz_id'];
        $exitosos = 0;
        $errores = [];
        $actualizados = [];

        try {
            $aprendiz = Aprendiz::find($aprendizId);
            if (! $aprendiz) {
                return ['exitosos' => 0, 'errores' => ["Aprendiz ID {$aprendizId} no encontrado"], 'actualizados' => []];
            }

            $asistenciaExistente = AsistenciaAprendiz::where('asistencia_id', $asistenciaId)
                ->where('aprendiz_ficha_id', $aprendizId)
                ->first();

            if ($asistenciaExistente) {
                if ($asistenciaExistente->hora_salida) {
                    $errores[] = "El aprendiz {$aprendiz->persona->getNombreCompletoAttribute()} ya tiene asistencia completa (entrada y salida) registrada";

                    return ['exitosos' => 0, 'errores' => $errores, 'actualizados' => []];
                }

                $asistenciaExistente->hora_salida = now();
                $asistenciaExistente->save();
                $exitosos++;
                $actualizados[] = [
                    'documento' => $aprendiz->persona->numero_documento,
                    'hora_salida' => $asistenciaExistente->hora_salida->format('h:i A'),
                ];

                return ['exitosos' => $exitosos, 'errores' => $errores, 'actualizados' => $actualizados];
            }

            $nuevaAsistencia = AsistenciaAprendiz::create([
                'asistencia_id' => $asistenciaId,
                'instructor_ficha_id' => $asistencia->instructor_ficha_id,
                'aprendiz_ficha_id' => $aprendizId,
                'hora_ingreso' => now(),
                'user_create_id' => auth()->id(),
            ]);

            $actualizados[] = [
                'documento' => $aprendiz->persona->numero_documento,
                'hora_ingreso' => $nuevaAsistencia->hora_ingreso->format('h:i A'),
            ];

            return ['exitosos' => 1, 'errores' => [], 'actualizados' => $actualizados];
        } catch (Exception $e) {
            Log::error('Error registrando asistencia para aprendiz ID '.$aprendizId.': '.$e->getMessage());

            return ['exitosos' => 0, 'errores' => ["Error al registrar asistencia para aprendiz ID {$aprendizId}"], 'actualizados' => []];
        }
    }

    /** @return array{exitosos: int, errores: list<string>} */
    private function procesarDocumentoManual(string $documentoManual, int $fichaId, Asistencia $asistencia, int $asistenciaId): array
    {
        try {
            $persona = Persona::where('numero_documento', $documentoManual)->first();
            if (! $persona) {
                return ['exitosos' => 0, 'errores' => ["No se encontró persona con documento {$documentoManual}"]];
            }

            $aprendiz = Aprendiz::where('persona_id', $persona->id)
                ->where('ficha_caracterizacion_id', $fichaId)
                ->first();

            if (! $aprendiz) {
                return ['exitosos' => 0, 'errores' => ["No se encontró aprendiz con documento {$documentoManual} en esta ficha"]];
            }

            $asistenciaExistente = AsistenciaAprendiz::where('asistencia_id', $asistenciaId)
                ->where('aprendiz_ficha_id', $aprendiz->id)
                ->first();

            if ($asistenciaExistente) {
                if ($asistenciaExistente->hora_salida) {
                    return ['exitosos' => 0, 'errores' => ["El aprendiz con documento {$documentoManual} ya tiene asistencia completa (entrada y salida) registrada"]];
                }

                $asistenciaExistente->hora_salida = now();
                $asistenciaExistente->save();

                return ['exitosos' => 1, 'errores' => []];
            }

            AsistenciaAprendiz::create([
                'asistencia_id' => $asistenciaId,
                'instructor_ficha_id' => $asistencia->instructor_ficha_id,
                'aprendiz_ficha_id' => $aprendiz->id,
                'hora_ingreso' => now(),
                'user_create_id' => auth()->id(),
            ]);

            return ['exitosos' => 1, 'errores' => []];
        } catch (Exception $e) {
            Log::error('Error registrando asistencia manual para documento '.$documentoManual.': '.$e->getMessage());

            return ['exitosos' => 0, 'errores' => ["Error al registrar asistencia manual para documento {$documentoManual}"]];
        }
    }
}
