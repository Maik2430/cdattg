<?php

namespace App\Policies\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use App\Models\User;

trait FichaCaracterizacionPolicyHelpers
{
    /**
     * Verifica si la ficha está asignada al instructor.
     */
    private function fichaAsignadaAInstructor(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Obtener el instructor asociado al usuario
        $instructor = $user->persona?->instructor;

        if (! $instructor) {
            return false;
        }

        // Verificar si la ficha está asignada directamente al instructor
        if ($fichaCaracterizacion->instructor_id === $instructor->id) {
            return true;
        }

        // Verificar si el instructor tiene fichas adicionales asignadas
        $fichasInstructor = $instructor->instructorFichas()
            ->where('ficha_id', $fichaCaracterizacion->id)
            ->exists();

        return $fichasInstructor;
    }
}
