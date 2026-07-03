<?php

namespace App\Policies;

use App\Models\Piso;
use App\Models\User;

class PisoPolicy
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
     * Determina si el usuario puede ver el listado de pisos.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER PISO');
    }

    /**
     * Determina si el usuario puede ver un piso concreto.
     */
    public function view(User $user, Piso $piso): bool
    {
        return $user->can('VER PISO');
    }

    /**
     * Determina si el usuario puede crear pisos.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR PISO');
    }

    /**
     * Determina si el usuario puede actualizar un piso.
     */
    public function update(User $user, Piso $piso): bool
    {
        return $user->can('EDITAR PISO');
    }

    /**
     * Determina si el usuario puede eliminar un piso.
     */
    public function delete(User $user, Piso $piso): bool
    {
        return $user->can('ELIMINAR PISO');
    }

    /**
     * Determina si el usuario puede restaurar un piso.
     */
    public function restore(User $user, Piso $piso): bool
    {
        return $user->can('ELIMINAR PISO');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un piso.
     */
    public function forceDelete(User $user, Piso $piso): bool
    {
        return $user->can('ELIMINAR PISO');
    }
}
