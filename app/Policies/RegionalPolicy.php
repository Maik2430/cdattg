<?php

namespace App\Policies;

use App\Models\Regional;
use App\Models\User;

class RegionalPolicy
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
     * Determina si el usuario puede ver el listado de regionales.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER REGIONAL');
    }

    /**
     * Determina si el usuario puede ver una regional concreta.
     */
    public function view(User $user, Regional $regional): bool
    {
        return $user->can('VER REGIONAL');
    }

    /**
     * Determina si el usuario puede crear regionales.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR REGIONAL');
    }

    /**
     * Determina si el usuario puede actualizar una regional.
     */
    public function update(User $user, Regional $regional): bool
    {
        return $user->can('EDITAR REGIONAL');
    }

    /**
     * Determina si el usuario puede eliminar una regional.
     */
    public function delete(User $user, Regional $regional): bool
    {
        return $user->can('ELIMINAR REGIONAL');
    }

    /**
     * Determina si el usuario puede restaurar una regional.
     */
    public function restore(User $user, Regional $regional): bool
    {
        return $user->can('ELIMINAR REGIONAL');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente una regional.
     */
    public function forceDelete(User $user, Regional $regional): bool
    {
        return $user->can('ELIMINAR REGIONAL');
    }
}
