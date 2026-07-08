<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait EspecialidadPolicy
{
    /**
     * Determine whether the user can manage instructor specialties.
     * Los instructores pueden gestionar sus propias especialidades.
     */
    public function gestionarEspecialidades(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('GESTIONAR ESPECIALIDADES INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propias especialidades
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can assign specialties to instructor.
     */
    public function asignarEspecialidad(User $user, Instructor $instructor): bool
    {
        return $this->gestionarEspecialidades($user, $instructor);
    }

    /**
     * Determine whether the user can remove specialty from instructor.
     */
    public function removerEspecialidad(User $user, Instructor $instructor): bool
    {
        return $this->gestionarEspecialidades($user, $instructor);
    }
}
