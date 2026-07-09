<?php

namespace App\Services\Concerns\InstructorFichaDias;

use Carbon\Carbon;

trait HandlesInstructorFichaDiasDiaHelpers
{
    /**
     * Verifica si hay conflicto entre dos rangos horarios.
     */
    protected function hayConflictoHorario(string $inicio1, string $fin1, string $inicio2, string $fin2): bool
    {
        $inicio1 = Carbon::parse($inicio1);
        $fin1 = Carbon::parse($fin1);
        $inicio2 = Carbon::parse($inicio2);
        $fin2 = Carbon::parse($fin2);

        return ! ($fin1->lte($inicio2) || $inicio1->gte($fin2));
    }

    /**
     * Obtiene el nombre del día según su ID.
     */
    protected function obtenerNombreDia(int $diaId): string
    {
        $dias = [
            12 => 'Lunes',
            13 => 'Martes',
            14 => 'Miércoles',
            15 => 'Jueves',
            16 => 'Viernes',
            17 => 'Sábado',
            18 => 'Domingo',
        ];

        return $dias[$diaId] ?? 'Desconocido';
    }
}
