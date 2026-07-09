<?php

namespace App\Services\Concerns\Persona;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

trait HandlesPersonaAuthHelpers
{
    protected function obtenerIdUsuarioAutenticado(): int
    {
        $userId = Auth::id();

        if ($userId === null) {
            throw new AuthenticationException('Debe iniciar sesión para realizar esta acción.');
        }

        return $userId;
    }
}
