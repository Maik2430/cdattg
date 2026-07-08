<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait CompetenciaPolicy
{
    /**
     * Determine whether the user can manage instructor competencies.
     * Los instructores pueden gestionar sus propias competencias.
     */
    public function gestionarCompetencias(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('GESTIONAR COMPETENCIAS INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propias competencias
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can assign competencies to instructor.
     */
    public function asignarCompetencia(User $user, Instructor $instructor): bool
    {
        return $this->gestionarCompetencias($user, $instructor);
    }

    /**
     * Determine whether the user can remove competency from instructor.
     */
    public function removerCompetencia(User $user, Instructor $instructor): bool
    {
        return $this->gestionarCompetencias($user, $instructor);
    }
}
