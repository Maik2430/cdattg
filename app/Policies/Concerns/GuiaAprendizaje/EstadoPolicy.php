<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\User;

trait EstadoPolicy
{
    /**
     * Determine whether the user can change guía status.
     * Solo usuarios con permisos específicos pueden cambiar estado.
     */
    public function cambiarEstado(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso específico
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
}
