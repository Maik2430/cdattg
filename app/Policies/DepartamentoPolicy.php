<?php

namespace App\Policies;

use App\Models\Departamento;
use App\Models\User;

class DepartamentoPolicy
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
     * Determina si el usuario puede ver el listado de departamentos.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede ver un departamento concreto.
     */
    public function view(User $user, Departamento $departamento): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede crear departamentos.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede actualizar un departamento.
     */
    public function update(User $user, Departamento $departamento): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede eliminar un departamento.
     */
    public function delete(User $user, Departamento $departamento): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede restaurar un departamento.
     */
    public function restore(User $user, Departamento $departamento): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un departamento.
     */
    public function forceDelete(User $user, Departamento $departamento): bool
    {
        return false;
    }
}
