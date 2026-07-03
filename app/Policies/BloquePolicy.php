<?php

namespace App\Policies;

use App\Models\Bloque;
use App\Models\User;

class BloquePolicy
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
     * Determina si el usuario puede ver el listado de bloques.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER BLOQUE');
    }

    /**
     * Determina si el usuario puede ver un bloque concreto.
     */
    public function view(User $user, Bloque $bloque): bool
    {
        return $user->can('VER BLOQUE');
    }

    /**
     * Determina si el usuario puede crear bloques.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR BLOQUE');
    }

    /**
     * Determina si el usuario puede actualizar un bloque.
     */
    public function update(User $user, Bloque $bloque): bool
    {
        return $user->can('EDITAR BLOQUE');
    }

    /**
     * Determina si el usuario puede eliminar un bloque.
     */
    public function delete(User $user, Bloque $bloque): bool
    {
        return $user->can('ELIMINAR BLOQUE');
    }

    /**
     * Determina si el usuario puede restaurar un bloque.
     */
    public function restore(User $user, Bloque $bloque): bool
    {
        return $user->can('ELIMINAR BLOQUE');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un bloque.
     */
    public function forceDelete(User $user, Bloque $bloque): bool
    {
        return $user->can('ELIMINAR BLOQUE');
    }
}
