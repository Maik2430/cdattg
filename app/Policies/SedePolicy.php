<?php

namespace App\Policies;

use App\Models\Sede;
use App\Models\User;

class SedePolicy
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
     * Determina si el usuario puede ver el listado de sedes.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER SEDE');
    }

    /**
     * Determina si el usuario puede ver una sede concreta.
     */
    public function view(User $user, Sede $sede): bool
    {
        return $user->can('VER SEDE');
    }

    /**
     * Determina si el usuario puede crear sedes.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR SEDE');
    }

    /**
     * Determina si el usuario puede actualizar una sede.
     */
    public function update(User $user, Sede $sede): bool
    {
        return $user->can('EDITAR SEDE');
    }

    /**
     * Determina si el usuario puede eliminar una sede.
     */
    public function delete(User $user, Sede $sede): bool
    {
        return $user->can('ELIMINAR SEDE');
    }

    /**
     * Determina si el usuario puede restaurar una sede.
     */
    public function restore(User $user, Sede $sede): bool
    {
        return $user->can('ELIMINAR SEDE');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente una sede.
     */
    public function forceDelete(User $user, Sede $sede): bool
    {
        return $user->can('ELIMINAR SEDE');
    }
}
