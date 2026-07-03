<?php

namespace App\Policies;

use App\Models\Tema;
use App\Models\User;

class TemaPolicy
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
     * Determina si el usuario puede ver el listado de temas.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER TEMA');
    }

    /**
     * Determina si el usuario puede ver un tema concreto.
     */
    public function view(User $user, Tema $tema): bool
    {
        return $user->can('VER TEMA');
    }

    /**
     * Determina si el usuario puede crear temas.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR TEMA');
    }

    /**
     * Determina si el usuario puede actualizar un tema.
     */
    public function update(User $user, Tema $tema): bool
    {
        return $user->can('EDITAR TEMA');
    }

    /**
     * Determina si el usuario puede eliminar un tema.
     */
    public function delete(User $user, Tema $tema): bool
    {
        return $user->can('ELIMINAR TEMA');
    }

    /**
     * Determina si el usuario puede restaurar un tema.
     */
    public function restore(User $user, Tema $tema): bool
    {
        return $user->can('ELIMINAR TEMA');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un tema.
     */
    public function forceDelete(User $user, Tema $tema): bool
    {
        return $user->can('ELIMINAR TEMA');
    }
}
