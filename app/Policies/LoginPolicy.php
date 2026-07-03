<?php

namespace App\Policies;

use App\Models\Login;
use App\Models\User;

class LoginPolicy
{
    /**
     * Otorga acceso total al SUPER ADMINISTRADOR antes de evaluar cada permiso.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('SUPER ADMINISTRADOR')) {
            return true;
        }

        return null;
    }

    /**
     * Determina si el usuario puede ver el listado de registros de login.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede ver un registro de login concreto.
     */
    public function view(User $user, Login $login): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede crear registros de login.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede actualizar un registro de login.
     */
    public function update(User $user, Login $login): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede eliminar un registro de login.
     */
    public function delete(User $user, Login $login): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede restaurar un registro de login.
     */
    public function restore(User $user, Login $login): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un registro de login.
     */
    public function forceDelete(User $user, Login $login): bool
    {
        return false;
    }
}
