<?php

namespace App\Services\Concerns\InstructorBusinessRules;

use App\Models\Instructor;

trait HandlesInstructorBusinessRulesEspecialidadHelpers
{
    /**
     * Verificar si el instructor tiene las especialidades requeridas
     */
    public function tieneEspecialidadesRequeridas(Instructor $instructor, ?string $especialidadRequerida): bool
    {
        if (! $especialidadRequerida) {
            return true; // Si no hay especialidad requerida, cualquier instructor puede tomar la ficha
        }

        if (is_array($instructor->especialidades)) {
            $especialidades = $instructor->especialidades;
        } elseif (is_string($instructor->especialidades)) {
            $especialidades = json_decode($instructor->especialidades, true);
        } else {
            $especialidades = [];
        }

        $especialidades = $especialidades ?? [];
        $especialidadPrincipal = $especialidades['principal'] ?? null;
        $especialidadesSecundarias = $especialidades['secundarias'] ?? [];

        // Verificar si la especialidad requerida coincide con la principal o alguna secundaria
        if ($especialidadPrincipal === $especialidadRequerida) {
            return true;
        }

        return in_array($especialidadRequerida, $especialidadesSecundarias);
    }

    /**
     * Validar que el instructor tenga experiencia mínima
     */
    public function validarExperienciaMinima(Instructor $instructor): bool
    {
        $anosExperiencia = $instructor->anos_experiencia ?? 0;

        return $anosExperiencia >= self::EXPERIENCIA_MINIMA;
    }
}
