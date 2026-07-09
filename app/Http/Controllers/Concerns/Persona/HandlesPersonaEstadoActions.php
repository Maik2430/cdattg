<?php

namespace App\Http\Controllers\Concerns\Persona;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaEstadoActions
{
    /**
     * Cambia el estado de una persona.
     *
     * Este método alterna el estado de una persona entre activo (1) e inactivo (0).
     * Si el estado actual es 1, se cambiará a 0 y viceversa.
     *
     * @param  int  $id  El ID de la persona cuyo estado se va a cambiar.
     * @return \Illuminate\Http\RedirectResponse Redirección de vuelta con un mensaje de éxito o error.
     */
    public function cambiarEstadoPersona($id)
    {
        $persona = Persona::findOrFail($id);
        $user = User::where('persona_id', $persona->id)->first();

        try {
            $persona->update(['status' => ! $persona->status]);
            $user->update(['status' => ! $user->status]);

            return redirect()->back()->with('success', 'Estado actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al cambiar estado de la persona (ID: {$id}): ".$e->getMessage());

            return redirect()->back()->with('error', 'No se pudo actualizar el estado.');
        }
    }
}
