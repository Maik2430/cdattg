<?php

namespace App\Services\Concerns\InstructorBusinessRules;

use App\Models\Instructor;

trait HandlesInstructorBusinessRulesDisponiblesActions
{
    /**
     * Obtener instructores disponibles para una ficha específica
     */
    public function obtenerInstructoresDisponibles(array $criterios): array
    {
        $especialidadRequerida = $criterios['especialidad_requerida'] ?? null;
        $regionalId = $criterios['regional_id'] ?? null;

        $query = Instructor::with(['persona', 'regional'])
            ->where('status', true);

        // Filtrar por regional
        if ($regionalId) {
            $query->where('regional_id', $regionalId);
        }

        // Filtrar por especialidad si se especifica
        if ($especialidadRequerida) {
            $query->where(function ($q) use ($especialidadRequerida) {
                $q->whereJsonContains('especialidades->principal', $especialidadRequerida)
                    ->orWhereJsonContains('especialidades->secundarias', $especialidadRequerida);
            });
        }

        $instructores = $query->get();
        $disponibles = [];

        foreach ($instructores as $instructor) {
            $disponibilidad = $this->verificarDisponibilidad($instructor, $criterios);

            if ($disponibilidad['disponible']) {
                $disponibles[] = [
                    'instructor' => $instructor,
                    'disponibilidad' => $disponibilidad,
                ];
            }
        }

        return $disponibles;
    }
}
