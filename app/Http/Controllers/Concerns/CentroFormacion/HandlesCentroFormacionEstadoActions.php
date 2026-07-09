<?php

namespace App\Http\Controllers\Concerns\CentroFormacion;

use App\Models\CentroFormacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesCentroFormacionEstadoActions
{
    /**
     * Cambiar el estado del centro de formación
     */
    public function cambiarEstado($id)
    {
        try {
            $centro = CentroFormacion::findOrFail($id);
            $nuevoStatus = $centro->status === 1 ? 0 : 1;

            $centro->update([
                'status' => $nuevoStatus,
                'user_update_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('success', 'Estado actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error("Error al cambiar el estado del centro de formación (ID: {$id}): ".$e->getMessage());

            return redirect()->back()
                ->with('error', 'No se pudo actualizar el estado');
        }
    }
}
