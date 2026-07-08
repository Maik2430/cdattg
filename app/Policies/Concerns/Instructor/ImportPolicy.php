<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\User;

trait ImportPolicy
{
    /**
     * Determine whether the user can import instructors.
     * Solo administradores pueden importar instructores.
     */
    public function importarInstructores(User $user): bool
    {
        return $user->can('CREAR INSTRUCTOR') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can download import template.
     */
    public function descargarPlantillaCSV(User $user): bool
    {
        return $this->importarInstructores($user);
    }
}
