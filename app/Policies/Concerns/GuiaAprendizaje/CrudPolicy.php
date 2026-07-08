<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\User;

trait CrudPolicy
{
    /**
     * Determine whether the user can view any models.
     * Los usuarios pueden ver guías según sus permisos y roles.
     */
    public function viewAny(User $user): bool
    {
        // Verificar permiso básico
        if (! $user->can('VER GUIA APRENDIZAJE')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Los instructores pueden ver guías de aprendizaje
        if ($user->hasRole('INSTRUCTOR')) {
            return true; // Se verificará en el controlador qué guías específicas
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Los usuarios pueden ver guías según sus permisos y contexto.
     */
    public function view(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso básico
        if (! $user->can('VER GUIA APRENDIZAJE')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, verificar permisos específicos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->instructorPuedeVerGuia($user, $guiaAprendizaje);
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     * Solo usuarios con permisos específicos pueden crear guías.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR GUIA APRENDIZAJE') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'INSTRUCTOR']);
    }

    /**
     * Determine whether the user can update the model.
     * Los usuarios pueden actualizar guías según sus permisos y contexto.
     */
    public function update(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso básico
        if (! $user->can('EDITAR GUIA APRENDIZAJE')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, verificar permisos específicos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->instructorPuedeEditarGuia($user, $guiaAprendizaje);
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     * Solo administradores pueden eliminar guías.
     */
    public function delete(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso básico
        if (! $user->can('ELIMINAR GUIA APRENDIZAJE')) {
            return false;
        }

        // Los instructores no pueden eliminar guías
        if ($user->hasRole('INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden eliminar
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $user->can('CREAR GUIA APRENDIZAJE') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $user->can('ELIMINAR GUIA APRENDIZAJE') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }
}
