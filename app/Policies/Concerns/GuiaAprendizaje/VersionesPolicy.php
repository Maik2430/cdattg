<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\User;

trait VersionesPolicy
{
    /**
     * Determine whether the user can manage guía versions.
     * Solo administradores pueden gestionar versiones.
     */
    public function gestionarVersiones(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $user->can('EDITAR GUIA APRENDIZAJE') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can create version.
     */
    public function crearVersion(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarVersiones($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can restore version.
     */
    public function restaurarVersion(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarVersiones($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can view version history.
     */
    public function historialVersiones(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }
}
