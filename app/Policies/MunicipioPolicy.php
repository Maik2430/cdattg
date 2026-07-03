<?php

namespace App\Policies;

use App\Models\Municipio;
use App\Models\User;

class MunicipioPolicy
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
     * Determina si el usuario puede ver el listado de municipios.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER MUNICIPIO');
    }

    /**
     * Determina si el usuario puede ver un municipio concreto.
     */
    public function view(User $user, Municipio $municipio): bool
    {
        return $user->can('VER MUNICIPIO');
    }

    /**
     * Determina si el usuario puede crear municipios.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR MUNICIPIO');
    }

    /**
     * Determina si el usuario puede actualizar un municipio.
     */
    public function update(User $user, Municipio $municipio): bool
    {
        return $user->can('EDITAR MUNICIPIO');
    }

    /**
     * Determina si el usuario puede eliminar un municipio.
     */
    public function delete(User $user, Municipio $municipio): bool
    {
        return $user->can('ELIMINAR MUNICIPIO');
    }

    /**
     * Determina si el usuario puede restaurar un municipio.
     */
    public function restore(User $user, Municipio $municipio): bool
    {
        return $user->can('ELIMINAR MUNICIPIO');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un municipio.
     */
    public function forceDelete(User $user, Municipio $municipio): bool
    {
        return $user->can('ELIMINAR MUNICIPIO');
    }
}
