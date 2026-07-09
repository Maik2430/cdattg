<?php

namespace App\Http\Controllers\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesGuiaAprendizajeApiActions
{
    public function apiIndex(): JsonResponse
    {
        try {
            $guiasAprendizaje = GuiasAprendizaje::with(['resultadosAprendizaje'])
                ->where('status', 1)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $guiasAprendizaje,
            ]);
        } catch (Exception $e) {
            Log::error('Error en API de guías de aprendizaje: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las guías de aprendizaje',
            ], 500);
        }
    }

    public function cambiarEstado(GuiasAprendizaje $guiaAprendizaje): RedirectResponse
    {
        try {
            $nuevoEstado = $guiaAprendizaje->status === 1 ? 0 : 1;
            $guiaAprendizaje->update([
                'status' => $nuevoEstado,
                'user_edit_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Estado cambiado exitosamente');
        } catch (Exception $e) {
            Log::error('Error al cambiar estado de guía: '.$e->getMessage(), [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('error', 'Error al cambiar el estado. Intente nuevamente.');
        }
    }
}
