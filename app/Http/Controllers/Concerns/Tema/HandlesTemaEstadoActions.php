<?php

namespace App\Http\Controllers\Concerns\Tema;

use App\Models\Tema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesTemaEstadoActions
{
    public function cambiarEstado(Tema $tema)
    {
        try {
            $nuevoEstado = $tema->status === 1 ? 0 : 1;
            $tema->update([
                'status' => $nuevoEstado,
                'user_edit_id' => Auth::id(), // Actualiza el usuario que realiza el cambio
            ]);

            return redirect()->back()->with('success', 'Estado cambiado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al cambiar el estado del tema: '.$e->getMessage());

            return redirect()->back()->with('error', 'No se pudo cambiar el estado');
        }
    }
}
