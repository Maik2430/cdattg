<?php

namespace App\Http\Controllers\Concerns\Aprendiz;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

trait HandlesAprendizEstadoActions
{
    /**
     * Cambia el estado de un aprendiz (activo/inactivo).
     *
     * @param  int  $id
     */
    public function cambiarEstado($id): RedirectResponse
    {
        try {
            $this->aprendizService->cambiarEstado($id);

            return redirect()->back()->with('success', 'Estado cambiado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del aprendiz: '.$e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
