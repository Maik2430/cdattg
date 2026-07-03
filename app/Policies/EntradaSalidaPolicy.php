<?php

namespace App\Policies;

use App\Models\EntradaSalida;
use App\Models\User;

class EntradaSalidaPolicy
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
     * Determina si el usuario puede ver el listado de entradas/salidas.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede ver un registro de entrada/salida concreto.
     */
    public function view(User $user, EntradaSalida $entradaSalida): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede crear registros de entrada/salida.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede actualizar un registro de entrada/salida.
     */
    public function update(User $user, EntradaSalida $entradaSalida): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede eliminar un registro de entrada/salida.
     */
    public function delete(User $user, EntradaSalida $entradaSalida): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede restaurar un registro de entrada/salida.
     */
    public function restore(User $user, EntradaSalida $entradaSalida): bool
    {
        return false;
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un registro de entrada/salida.
     */
    public function forceDelete(User $user, EntradaSalida $entradaSalida): bool
    {
        return false;
    }
}
