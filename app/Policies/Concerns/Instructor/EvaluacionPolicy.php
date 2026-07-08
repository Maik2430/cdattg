<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait EvaluacionPolicy
{
    /**
     * Determine whether the user can manage instructor evaluations.
     * Solo administradores pueden gestionar evaluaciones.
     */
    public function gestionarEvaluaciones(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('GESTIONAR EVALUACIONES INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden gestionar evaluaciones
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can create evaluations.
     */
    public function crearEvaluacion(User $user, Instructor $instructor): bool
    {
        return $this->gestionarEvaluaciones($user, $instructor);
    }

    /**
     * Determine whether the user can save evaluations.
     */
    public function guardarEvaluacion(User $user, Instructor $instructor): bool
    {
        return $this->gestionarEvaluaciones($user, $instructor);
    }
}
