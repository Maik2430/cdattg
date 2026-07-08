<?php

namespace App\Policies\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use App\Models\User;

trait CrudPolicy
{
    /**
     * Determine whether the user can view any models.
     * Los instructores solo ven fichas asignadas a ellos.
     */
    public function viewAny(User $user): bool
    {
        // Verificar permiso básico
        if (! $user->can('VER FICHA CARACTERIZACION')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Los instructores pueden ver fichas si tienen el permiso
        if ($user->hasRole('INSTRUCTOR')) {
            return true; // Se verificará en el controlador qué fichas específicas
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Los instructores solo pueden ver fichas asignadas a ellos.
     */
    public function view(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso básico
        if (! $user->can('VER FICHA CARACTERIZACION')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, verificar que la ficha esté asignada a él
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->fichaAsignadaAInstructor($user, $fichaCaracterizacion);
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR FICHA CARACTERIZACION');
    }

    /**
     * Determine whether the user can update the model.
     * Los instructores solo pueden actualizar fichas asignadas a ellos.
     */
    public function update(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso básico
        if (! $user->can('EDITAR FICHA CARACTERIZACION')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, verificar que la ficha esté asignada a él
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->fichaAsignadaAInstructor($user, $fichaCaracterizacion);
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     * Solo administradores pueden eliminar fichas.
     */
    public function delete(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso básico
        if (! $user->can('ELIMINAR FICHA CARACTERIZACION')) {
            return false;
        }

        // Los instructores no pueden eliminar fichas
        if ($user->hasRole('INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden eliminar
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $user->can('CREAR FICHA CARACTERIZACION') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $user->can('ELIMINAR FICHA CARACTERIZACION') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }
}
