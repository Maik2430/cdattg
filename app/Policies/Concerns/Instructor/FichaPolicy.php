<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait FichaPolicy
{
    /**
     * Determine whether the user can view assigned fichas.
     * Los instructores pueden ver sus propias fichas asignadas.
     */
    public function fichasAsignadas(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('VER FICHAS INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede ver sus propias fichas
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can view active fichas.
     */
    public function fichasActivas(User $user, Instructor $instructor): bool
    {
        return $this->fichasAsignadas($user, $instructor);
    }

    /**
     * Determine whether the user can view fichas history.
     */
    public function historialFichas(User $user, Instructor $instructor): bool
    {
        return $this->fichasAsignadas($user, $instructor);
    }

    /**
     * Determine whether the user can assign fichas to instructor.
     * Solo administradores pueden asignar fichas.
     */
    public function asignarFicha(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('ASIGNAR FICHA INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden asignar fichas
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can unassign fichas from instructor.
     */
    public function desasignarFicha(User $user, Instructor $instructor): bool
    {
        return $this->asignarFicha($user, $instructor);
    }

    /**
     * Determine whether the user can view assigned fichas.
     */
    public function verFichasAsignadas(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('VER FICHAS ASIGNADAS')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede ver sus propias fichas
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }
}
