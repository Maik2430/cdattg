<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Models\Asistencia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistenceBulkRegistration
{
    use HandlesQrAsistenceBulkRegistrationProcessors;

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
}
