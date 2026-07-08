<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait EstadoPolicy
{
    /**
     * Determine whether the user can change instructor status.
     * Solo administradores pueden cambiar estado de instructores.
     */
    public function cambiarEstado(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('CAMBIAR ESTADO INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden cambiar estado
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can change user status.
     * Solo administradores pueden cambiar estado de usuarios.
     */
    public function cambiarEstadoUsuario(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('CAMBIAR ESTADO USUARIO')) {
            return false;
        }

        // Solo super administradores y administradores pueden cambiar estado
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }
}
