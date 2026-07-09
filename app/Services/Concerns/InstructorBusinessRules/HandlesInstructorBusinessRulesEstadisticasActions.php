<?php

namespace App\Services\Concerns\InstructorBusinessRules;

use App\Models\Instructor;

trait HandlesInstructorBusinessRulesEstadisticasActions
{
    /**
     * Obtener estadísticas de carga de trabajo por instructor
     */
    public function obtenerEstadisticasCargaTrabajo(): array
    {
        $instructores = Instructor::with(['instructorFichas.ficha'])
            ->where('status', true)
            ->get();

        return $instructores->map(function (Instructor $instructor) {
            $fichasActivas = $this->contarFichasActivas($instructor);
            $totalHoras = $this->sumarTotalHorasInstructor($instructor, true);

            return [
                'instructor_id' => $instructor->id,
                'nombre' => $instructor->nombre_completo,
                'fichas_activas' => $fichasActivas,
                'total_horas' => $totalHoras,
                'carga_alta' => $fichasActivas >= 4 || $totalHoras >= 200,
                'disponible_para_mas' => $fichasActivas < self::MAX_FICHAS_ACTIVAS,
            ];
        })->toArray();
    }
}
