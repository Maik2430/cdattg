<?php

namespace App\Policies\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use App\Models\User;

trait EstadoPolicy
{
    /**
     * Determine whether the user can change ficha status.
     * Los instructores solo pueden cambiar estado de fichas asignadas.
     */
    public function cambiarEstado(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso específico
        if (! $user->can('CAMBIAR ESTADO FICHA')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, verificar que la ficha esté asignada a él
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->fichaAsignadaAInstructor($user, $fichaCaracterizacion);
        }

        return true;
    }
}
