<?php

namespace App\Policies\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use App\Models\User;

trait InstructoresPolicy
{
    /**
     * Determine whether the user can manage instructors for a ficha.
     * Solo administradores pueden gestionar instructores.
     */
    public function gestionarInstructores(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso específico
        if (! $user->can('GESTIONAR INSTRUCTORES FICHA')) {
            return false;
        }

        // Solo super administradores y administradores pueden gestionar instructores
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can assign instructors to a ficha.
     */
    public function asignarInstructores(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->gestionarInstructores($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can unassign an instructor from a ficha.
     */
    public function desasignarInstructor(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->gestionarInstructores($user, $fichaCaracterizacion);
    }
}
