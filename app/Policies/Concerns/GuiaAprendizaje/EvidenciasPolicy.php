<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\User;

trait EvidenciasPolicy
{
    /**
     * Determine whether the user can manage evidencias/actividades.
     * Los usuarios pueden gestionar evidencias según sus permisos.
     */
    public function gestionarEvidencias(User $user, GuiasAprendizaje $guiaAprendizaje): bool
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

    /**
     * Determine whether the user can associate evidencia to guía.
     */
    public function asociarEvidencia(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarEvidencias($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can remove evidencia from guía.
     */
    public function desasociarEvidencia(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarEvidencias($user, $guiaAprendizaje);
    }
}
