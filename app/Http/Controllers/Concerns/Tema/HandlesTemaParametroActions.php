<?php

namespace App\Http\Controllers\Concerns\Tema;

use App\Models\Parametro;
use App\Models\Tema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesTemaParametroActions
{
    public function updateParametrosTemas(Request $request)
    {
        try {
            $data = $request->validate([
                'tema_id' => 'required|integer|exists:temas,id',
                'parametros' => 'nullable|array',
                'parametros.*' => 'integer|exists:parametros,id',
                'estados' => 'nullable|array',
            ]);

            $parametros = $data['parametros'] ?? [];
            $estados = $data['estados'] ?? array_fill(0, count($parametros), 1);

            $this->temaService->actualizarParametros($data['tema_id'], $parametros, $estados);

            return redirect()->back()->with('success', 'Parámetros actualizados exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar parámetros: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al actualizar parámetros.');
        }
    }

    public function cambiarEstadoParametro(Tema $tema, Parametro $parametro)
    {
        try {
            DB::transaction(function () use ($tema, $parametro) {
                // Obtenemos el registro pivote con los datos cargados
                $parametroAdjunto = $tema->parametros()->where('parametros.id', $parametro->id)->first();

                if (! $parametroAdjunto) {
                    throw new \Exception('El parámetro no está vinculado al tema');
                }

                // Accedemos al valor del campo 'status' desde el pivot
                $nuevoEstado = $parametroAdjunto->pivot->status == 1 ? 0 : 1;

                // Actualizamos el registro del pivot usando el método updateExistingPivot()
                $tema->parametros()->updateExistingPivot($parametro->id, [
                    'status' => $nuevoEstado,
                    'user_edit_id' => Auth::id(),
                    'updated_at' => now(), // Forzamos la actualización del timestamp en el pivot
                ]);

                // Actualizamos el tema (opcional, para registrar la acción)
                $tema->update(['user_edit_id' => Auth::id()]);
                $tema->touch();
            });

            return redirect()->back()->with('success', 'Estado cambiado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al cambiar el estado del parámetro: '.$e->getMessage());

            return redirect()->back()->with('error', 'No se pudo cambiar el estado');
        }
    }

    public function eliminarParametro(Tema $tema, Parametro $parametro)
    {
        try {
            DB::transaction(function () use ($tema, $parametro) {
                // Desvincular el parámetro del tema
                $tema->parametros()->detach($parametro->id);
                // Actualizar el timestamp y registrar el usuario que realiza la acción
                $tema->touch();
                $tema->update(['user_edit_id' => Auth::id()]);
            });

            return redirect()->back()->with('success', 'Parámetro eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar el parámetro: '.$e->getMessage());

            return redirect()->back()->with('error', 'No se pudo eliminar el parámetro');
        }
    }
}
