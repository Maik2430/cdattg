<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\User;

trait ResultadosPolicy
{
    /**
     * Determine whether the user can manage resultados de aprendizaje.
     * Los usuarios pueden gestionar resultados según sus permisos.
     */
    public function gestionarResultados(User $user, GuiasAprendizaje $guiaAprendizaje): bool
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
     * Determine whether the user can associate resultado to guía.
     */
    public function asociarResultado(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarResultados($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can remove resultado from guía.
     */
    public function desasociarResultado(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarResultados($user, $guiaAprendizaje);
    }
}
