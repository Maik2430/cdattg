<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait CrudPolicy
{
    /**
     * Determine whether the user can view any models.
     * Los instructores pueden ver otros instructores de su regional.
     */
    public function viewAny(User $user): bool
    {
        // Verificar permiso básico
        if (! $user->can('VER INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Los instructores pueden ver otros instructores
        if ($user->hasRole('INSTRUCTOR')) {
            return true; // Se verificará en el controlador qué instructores específicos
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Los instructores pueden ver su propio perfil y otros de su regional.
     */
    public function view(User $user, Instructor $instructor): bool
    {
        // Verificar permiso básico
        if (! $user->can('VER INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, verificar permisos específicos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->instructorPuedeVer($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     * Solo administradores pueden crear instructores.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR INSTRUCTOR') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can update the model.
     * Los instructores solo pueden actualizar su propio perfil.
     */
    public function update(User $user, Instructor $instructor): bool
    {
        // Verificar permiso básico
        if (! $user->can('EDITAR INSTRUCTOR')) {
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
     * Determine whether the user can delete the model.
     * Solo administradores pueden eliminar instructores.
     */
    public function delete(User $user, Instructor $instructor): bool
    {
        // Verificar permiso básico
        if (! $user->can('ELIMINAR INSTRUCTOR')) {
            return false;
        }

        // Los instructores no pueden eliminar otros instructores
        if ($user->hasRole('INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden eliminar
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Instructor $instructor): bool
    {
        return $user->can('CREAR INSTRUCTOR') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Instructor $instructor): bool
    {
        return $user->can('ELIMINAR INSTRUCTOR') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }
}
