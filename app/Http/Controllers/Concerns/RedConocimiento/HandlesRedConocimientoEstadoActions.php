<?php

namespace App\Http\Controllers\Concerns\RedConocimiento;

use App\Models\RedConocimiento;
use Illuminate\Support\Facades\Log;

trait HandlesRedConocimientoEstadoActions
{
    /**
     * Cambiar el estado de una red de conocimiento.
     */
    public function cambiarEstado(RedConocimiento $redConocimiento)
    {
        try {
            $this->redService->cambiarEstado($redConocimiento->id);

            $mensaje = $redConocimiento->status === 0
                ? 'Red de conocimiento activada exitosamente.'
                : 'Red de conocimiento desactivada exitosamente.';

            return redirect()->back()->with('success', $mensaje);
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al cambiar estado.');
        }
    }
}
