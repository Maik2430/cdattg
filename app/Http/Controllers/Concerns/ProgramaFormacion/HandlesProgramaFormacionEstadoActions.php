<?php

namespace App\Http\Controllers\Concerns\ProgramaFormacion;

use App\Models\ProgramaFormacion;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesProgramaFormacionEstadoActions
{
    public function cambiarEstado(string $id): RedirectResponse
    {
        try {
            $programa = ProgramaFormacion::findOrFail($id);
            $programa->status = ! $programa->status;

            if ($programa->save()) {
                Log::info('Estado del programa cambiado', [
                    'programa_id' => $id,
                    'nuevo_estado' => $programa->status ? 'activo' : 'inactivo',
                    'usuario_id' => Auth::id(),
                ]);

                return redirect()->back()->with('success', 'Estado del programa actualizado exitosamente.');
            }

            return redirect()->back()->with('error', 'Error al cambiar el estado del programa.');
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del programa', [
                'programa_id' => $id,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Error interno al cambiar el estado del programa.');
        }
    }
}
