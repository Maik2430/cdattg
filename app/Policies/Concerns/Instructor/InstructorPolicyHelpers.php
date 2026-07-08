<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait InstructorPolicyHelpers
{
    /**
     * Verifica si el usuario es el mismo instructor.
     */
    private function esElMismoInstructor(User $user, Instructor $instructor): bool
    {
        // Obtener el instructor asociado al usuario
        $instructorUsuario = $user->persona?->instructor;

        if (! $instructorUsuario) {
            return false;
        }

        return $instructorUsuario->id === $instructor->id;
    }

    /**
     * Verifica si el instructor puede ver otro instructor.
     * Los instructores pueden ver otros instructores de su regional.
     */
    private function instructorPuedeVer(User $user, Instructor $instructor): bool
    {
        // Obtener el instructor asociado al usuario
        $instructorUsuario = $user->persona?->instructor;

        if (! $instructorUsuario) {
            return false;
        }

        // Puede ver su propio perfil
        if ($instructorUsuario->id === $instructor->id) {
            return true;
        }

        // Puede ver otros instructores de su regional
        return $instructorUsuario->regional_id === $instructor->regional_id;
    }
}
