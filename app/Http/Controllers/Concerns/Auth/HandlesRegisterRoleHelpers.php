<?php

namespace App\Http\Controllers\Concerns\Auth;

use App\Models\Complementarios\AspiranteComplementario;
use App\Models\Persona;
use App\Models\User;

trait HandlesRegisterRoleHelpers
{
    private function actualizarRolesSegunInscripcion(Persona $persona, User $user): void
    {
        if (! $this->tieneInscripciones($persona)) {
            return;
        }

        $user->removeRole('VISITANTE');
        $user->assignRole('ASPIRANTE');
    }

    private function tieneInscripciones(Persona $persona): bool
    {
        return AspiranteComplementario::where('persona_id', $persona->id)->exists();
    }
}
