<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait ReportePolicy
{
    /**
     * Determine whether the user can view instructor reports.
     */
    public function reportePorRegional(User $user): bool
    {
        return $user->can('VER INSTRUCTOR') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can view instructor reports by status.
     */
    public function reportePorEstado(User $user): bool
    {
        return $this->reportePorRegional($user);
    }

    /**
     * Determine whether the user can view instructor statistics.
     */
    public function estadisticas(User $user): bool
    {
        return $user->can('VER INSTRUCTOR');
    }

    /**
     * Determine whether the user can export instructors.
     */
    public function exportar(User $user): bool
    {
        return $user->can('VER INSTRUCTOR') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can view available instructors.
     */
    public function disponibles(User $user): bool
    {
        return $user->can('VER INSTRUCTOR');
    }

    /**
     * Determine whether the user can view busy instructors.
     */
    public function ocupados(User $user): bool
    {
        return $user->can('VER INSTRUCTOR');
    }

    /**
     * Determine whether the user can check instructor availability.
     */
    public function verificarDisponibilidad(User $user, Instructor $instructor): bool
    {
        return $this->view($user, $instructor);
    }
}
