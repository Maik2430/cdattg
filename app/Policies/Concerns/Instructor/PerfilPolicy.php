<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait PerfilPolicy
{
    /**
     * Determine whether the user can view professional profile.
     * Los instructores pueden ver su propio perfil profesional.
     */
    public function perfilProfesional(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('VER PERFIL PROFESIONAL INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede ver su propio perfil
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can edit professional profile.
     */
    public function editarPerfilProfesional(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('EDITAR PERFIL PROFESIONAL INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede editar su propio perfil
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can update professional profile.
     */
    public function actualizarPerfilProfesional(User $user, Instructor $instructor): bool
    {
        return $this->editarPerfilProfesional($user, $instructor);
    }

    /**
     * Determine whether the user can change instructor password.
     * Los instructores pueden cambiar su propia contraseña.
     */
    public function cambiarContraseña(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('CAMBIAR CONTRASEÑA INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede cambiar su propia contraseña
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can update password.
     */
    public function actualizarContraseña(User $user, Instructor $instructor): bool
    {
        return $this->cambiarContraseña($user, $instructor);
    }

    /**
     * Determine whether the user can reset password.
     * Solo administradores pueden resetear contraseñas.
     */
    public function resetearContraseña(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('RESETEAR CONTRASEÑA INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden resetear contraseñas
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }
}
