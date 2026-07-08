<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Models\Evidencias;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistenceEvidenciaActions
{
    public function storeEvidencia(Request $request)
    {
        Log::info('=== DEBUG STORE EVIDENCIA ===');
        Log::info('Request data: '.json_encode($request->all()));

        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'caracterizacion_id' => 'required|integer',
                'ficha_id' => 'required|integer',
            ]);

            Log::info('Validación pasada');
            Log::info('Creando evidencia con nombre: '.$request->nombre);

            $nombreOriginal = $request->nombre;
            $nombreFinal = $nombreOriginal;
            $contador = 1;

            while (Evidencias::where('nombre', $nombreFinal)->exists()) {
                $nombreFinal = $nombreOriginal.' '.$contador;
                $contador++;
            }

            if ($nombreFinal !== $nombreOriginal) {
                Log::info('Nombre duplicado, usando: '.$nombreFinal);
            }

            $evidencia = Evidencias::create([
                'nombre' => $nombreFinal,
                'id_estado' => 1,
                'fecha_evidencia' => now(),
                'user_create_id' => auth()->id(),
            ]);

            Log::info('Evidencia creada con ID: '.$evidencia->id);
            Log::info('Evidencia datos: '.json_encode($evidencia->toArray()));

            $responseData = [
                'success' => true,
                'message' => 'Evidencia creada exitosamente',
                'evidencia_id' => $evidencia->id,
                'evidencia_nombre' => $evidencia->nombre,
            ];

            Log::info('Respuesta JSON a enviar: '.json_encode($responseData));

            return response()->json($responseData);
        } catch (Exception $e) {
            Log::error('Error en storeEvidencia: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la evidencia: '.$e->getMessage(),
            ], 500);
        }
    }

    public function setSessionAlert(Request $request)
    {
        try {
            $request->validate([
                'key' => 'required|string|in:success,error,warning,info',
                'message' => 'required|string|max:255',
            ]);

            $key = $request->input('key');
            $message = $request->input('message');

            session()->flash($key, $message);

            return response()->json([
                'status' => 'success',
                'message' => 'Alerta guardada en sesión',
            ]);
        } catch (Exception $e) {
            Log::error('Error al guardar alerta en sesión: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error al guardar alerta en sesión',
            ], 500);
        }
    }
}
