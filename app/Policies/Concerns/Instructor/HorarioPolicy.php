<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait HorarioPolicy
{
    /**
     * Determine whether the user can manage instructor schedules.
     * Los instructores pueden gestionar sus propios horarios.
     */
    public function gestionarHorarios(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('GESTIONAR HORARIOS INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propios horarios
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can update instructor schedules.
     */
    public function actualizarHorarios(User $user, Instructor $instructor): bool
    {
        return $this->gestionarHorarios($user, $instructor);
    }
}
