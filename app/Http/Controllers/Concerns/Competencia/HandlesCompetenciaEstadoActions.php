<?php

namespace App\Http\Controllers\Concerns\Competencia;

use App\Models\Competencia;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesCompetenciaEstadoActions
{
    public function cambiarEstado(Competencia $competencia): RedirectResponse
    {
        try {
            $nuevoEstado = $competencia->status === 1 ? 0 : 1;
            $competencia->status = $nuevoEstado;
            $competencia->user_edit_id = Auth::id();
            $competencia->save();

            $estadoTexto = $nuevoEstado === 1 ? 'activa' : 'inactiva';

            Log::info('Estado de competencia cambiado', [
                'competencia_id' => $competencia->id,
                'codigo' => $competencia->codigo,
                'nuevo_estado' => $nuevoEstado,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('success', "Competencia '{$competencia->codigo}' marcada como {$estadoTexto}.");
        } catch (Exception $e) {
            Log::error('Error al cambiar estado de competencia: '.$e->getMessage(), [
                'competencia_id' => $competencia->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('error', 'Error al cambiar el estado. Intente nuevamente.');
        }
    }
}
