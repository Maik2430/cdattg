<?php

namespace App\Services\Concerns\InstructorFichaDias;

use App\Models\InstructorFichaDias;

trait HandlesInstructorFichaDiasReadActions
{
    /**
     * Obtiene los días asignados a un instructor en una ficha.
     */
    public function obtenerDiasAsignados(int $instructorFichaId): array
    {
        $dias = InstructorFichaDias::where('instructor_ficha_id', $instructorFichaId)
            ->with('dia')
            ->get();

        return $dias->map(function ($dia) {
            return [
                'id' => $dia->id,
                'dia_id' => $dia->dia_id,
                'dia_nombre' => $this->obtenerNombreDia($dia->dia_id),
                'hora_inicio' => $dia->hora_inicio,
                'hora_fin' => $dia->hora_fin,
            ];
        })->toArray();
    }
}
