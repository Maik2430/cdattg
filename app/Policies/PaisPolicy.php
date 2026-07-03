<?php

namespace App\Policies;

use App\Models\Pais;
use App\Models\User;

class PaisPolicy
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
     * Determina si el usuario puede ver el listado de países.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede ver un país concreto.
     */
    public function view(User $user, Pais $pais): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede crear países.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede actualizar un país.
     */
    public function update(User $user, Pais $pais): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede eliminar un país.
     */
    public function delete(User $user, Pais $pais): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede restaurar un país.
     */
    public function restore(User $user, Pais $pais): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un país.
     */
    public function forceDelete(User $user, Pais $pais): bool
    {
        return false;
    }
}
