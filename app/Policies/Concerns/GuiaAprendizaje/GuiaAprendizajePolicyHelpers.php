<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\User;

trait GuiaAprendizajePolicyHelpers
{
    /**
     * Verifica si el instructor puede ver la guía de aprendizaje.
     * Los instructores pueden ver guías relacionadas con sus competencias.
     */
    private function instructorPuedeVerGuia(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Obtener el instructor asociado al usuario
        $instructor = $user->persona?->instructor;

        if (! $instructor) {
            return false;
        }

        // Verificar si la guía está activa
        if ($guiaAprendizaje->status != 1) {
            return false;
        }

        // Los instructores pueden ver todas las guías activas
        // En el futuro se puede implementar lógica más específica
        // basada en competencias o regionales
        return true;
    }

    /**
     * Verifica si el instructor puede editar la guía de aprendizaje.
     * Los instructores pueden editar guías que han creado o que están asignadas.
     */
    private function instructorPuedeEditarGuia(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Obtener el instructor asociado al usuario
        $instructor = $user->persona?->instructor;

        if (! $instructor) {
            return false;
        }

        // Verificar si el instructor creó la guía
        if ($guiaAprendizaje->user_create_id === $user->id) {
            return true;
        }

        // En el futuro se puede implementar lógica para verificar
        // si el instructor está asignado a la guía o tiene competencias relacionadas
        return false;
    }
}
