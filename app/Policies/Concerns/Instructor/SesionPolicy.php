<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait SesionPolicy
{
    /**
     * Determine whether the user can manage instructor sessions.
     * Los instructores pueden gestionar sus propias sesiones.
     */
    public function gestionarSesiones(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('GESTIONAR SESIONES INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propias sesiones
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can view active sessions.
     */
    public function sesionesActivas(User $user, Instructor $instructor): bool
    {
        return $this->gestionarSesiones($user, $instructor);
    }

    /**
     * Determine whether the user can view session history.
     */
    public function historialSesiones(User $user, Instructor $instructor): bool
    {
        return $this->gestionarSesiones($user, $instructor);
    }

    /**
     * Determine whether the user can close session.
     */
    public function cerrarSesion(User $user, Instructor $instructor): bool
    {
        return $this->gestionarSesiones($user, $instructor);
    }
}
