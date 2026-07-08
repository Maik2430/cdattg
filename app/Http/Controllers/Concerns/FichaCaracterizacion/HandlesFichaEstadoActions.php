<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaEstadoActions
{
    /**
     * Cambia el estado de una ficha (activar/desactivar).
     *
     * @param  string  $id  El ID de la ficha.
     * @return \Illuminate\Http\JsonResponse Resultado del cambio de estado.
     */
    public function cambiarEstadoFicha(Request $request, string $id)
    {
        try {
            Log::info('Cambio de estado de ficha de caracterización', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'timestamp' => now(),
            ]);

            $request->validate([
                'status' => 'required|boolean',
            ], [
                'status.required' => 'El estado es obligatorio.',
                'status.boolean' => 'El estado debe ser verdadero o falso.',
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);
            $estadoAnterior = $ficha->status;

            DB::beginTransaction();

            $ficha->status = $request->input('status');
            $ficha->user_edit_id = Auth::id();

            if ($ficha->save()) {
                DB::commit();

                $mensaje = $ficha->status ? 'Ficha activada exitosamente' : 'Ficha desactivada exitosamente';

                Log::info('Estado de ficha cambiado exitosamente', [
                    'ficha_id' => $ficha->id,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo' => $ficha->status,
                    'user_id' => Auth::id(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $mensaje,
                    'data' => [
                        'ficha_id' => $ficha->id,
                        'numero_ficha' => $ficha->ficha,
                        'estado_anterior' => $estadoAnterior,
                        'estado_nuevo' => $ficha->status,
                    ],
                ], 200);
            }

            DB::rollBack();
            throw new \Exception('Error al cambiar el estado de la ficha');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación al cambiar estado de ficha', [
                'ficha_id' => $id,
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Intento de cambiar estado de ficha inexistente', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'La ficha solicitada no existe',
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al cambiar estado de ficha', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado de la ficha',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
