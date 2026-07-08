<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\User;

trait PlantillasPolicy
{
    /**
     * Determine whether the user can manage guía templates.
     * Solo administradores pueden gestionar plantillas.
     */
    public function gestionarPlantillas(User $user): bool
    {
        return $user->can('CREAR GUIA APRENDIZAJE') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can create template.
     */
    public function crearPlantilla(User $user): bool
    {
        return $this->gestionarPlantillas($user);
    }

    /**
     * Determine whether the user can update template.
     */
    public function actualizarPlantilla(User $user): bool
    {
        return $this->gestionarPlantillas($user);
    }

    /**
     * Determine whether the user can delete template.
     */
    public function eliminarPlantilla(User $user): bool
    {
        return $this->gestionarPlantillas($user);
    }
}
