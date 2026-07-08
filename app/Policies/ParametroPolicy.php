<?php

namespace App\Policies;

use App\Models\parametro;
use App\Models\User;

class ParametroPolicy
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
     * Determina si el usuario puede ver el listado de parámetros.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER PARAMETRO');
    }

    /**
     * Determina si el usuario puede ver un parámetro concreto.
     */
    public function view(User $user, parametro $parametro): bool
    {
        return $user->can('VER PARAMETRO');
    }

    /**
     * Determina si el usuario puede crear parámetros.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR PARAMETRO');
    }

    /**
     * Determina si el usuario puede actualizar un parámetro.
     */
    public function update(User $user, parametro $parametro): bool
    {
        return $user->can('EDITAR PARAMETRO');
    }

    /**
     * Determina si el usuario puede eliminar un parámetro.
     */
    public function delete(User $user, parametro $parametro): bool
    {
        return $user->can('ELIMINAR PARAMETRO');
    }

    /**
     * Determina si el usuario puede restaurar un parámetro.
     */
    public function restore(User $user, parametro $parametro): bool
    {
        return $user->can('ELIMINAR PARAMETRO');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un parámetro.
     */
    public function forceDelete(User $user, parametro $parametro): bool
    {
        return $user->can('ELIMINAR PARAMETRO');
    }
}
