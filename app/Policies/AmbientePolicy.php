<?php

namespace App\Policies;

use App\Models\Ambiente;
use App\Models\User;

class AmbientePolicy
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
     * Determina si el usuario puede ver el listado de ambientes.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER AMBIENTE');
    }

    /**
     * Determina si el usuario puede ver un ambiente concreto.
     */
    public function view(User $user, Ambiente $ambiente): bool
    {
        return $user->can('VER AMBIENTE');
    }

    /**
     * Determina si el usuario puede crear ambientes.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR AMBIENTE');
    }

    /**
     * Determina si el usuario puede actualizar un ambiente.
     */
    public function update(User $user, Ambiente $ambiente): bool
    {
        return $user->can('EDITAR AMBIENTE');
    }

    /**
     * Determina si el usuario puede eliminar un ambiente.
     */
    public function delete(User $user, Ambiente $ambiente): bool
    {
        return $user->can('ELIMINAR AMBIENTE');
    }

    /**
     * Determina si el usuario puede restaurar un ambiente.
     */
    public function restore(User $user, Ambiente $ambiente): bool
    {
        return $user->can('ELIMINAR AMBIENTE');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un ambiente.
     */
    public function forceDelete(User $user, Ambiente $ambiente): bool
    {
        return $user->can('ELIMINAR AMBIENTE');
    }
}
