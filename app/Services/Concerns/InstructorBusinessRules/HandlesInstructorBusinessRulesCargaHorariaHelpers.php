<?php

namespace App\Services\Concerns\InstructorBusinessRules;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Carbon\Carbon;

trait HandlesInstructorBusinessRulesCargaHorariaHelpers
{
    /**
     * Calcular carga horaria semanal del instructor
     *
     * @param  int|null  $jornadaId  Jornada de la nueva ficha (para filtrar solo misma jornada)
     * @return int Total de horas semanales
     */
    public function calcularCargaHorariaSemanal(
        Instructor $instructor,
        Carbon $fechaInicio,
        Carbon $fechaFin,
        int $horasNuevaFicha = 0,
        ?int $jornadaId = null
    ): int {
        // Obtener fichas activas en el período
        $fichasActivas = $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) use ($fechaInicio, $fechaFin, $jornadaId) {
                $q->where('status', true)
                    ->where(function ($query) use ($fechaInicio, $fechaFin) {
                        $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                            ->orWhere(function ($subQuery) use ($fechaInicio, $fechaFin) {
                                $subQuery->where('fecha_inicio', '<=', $fechaInicio)
                                    ->where('fecha_fin', '>=', $fechaFin);
                            });
                    });

                // Filtrar solo por la misma jornada si se proporciona
                if ($jornadaId) {
                    $q->where('jornada_id', $jornadaId);
                }
            })
            ->get();

        $totalHoras = 0;

        // Sumar horas de fichas existentes en la misma jornada
        foreach ($fichasActivas as $instructorFicha) {
            $totalHoras += $this->calcularHorasSemanalesFicha($instructorFicha->ficha);
        }

        // Agregar horas de la nueva ficha
        $totalHoras += $horasNuevaFicha;

        return $totalHoras;
    }

    /**
     * Calcular horas semanales de una ficha específica
     */
    protected function calcularHorasSemanalesFicha(FichaCaracterizacion $ficha): int
    {
        // Calcular días de formación por semana
        $diasFormacion = $ficha->diasFormacion->count();

        // Calcular horas por día (asumiendo 8 horas por día como estándar SENA)
        $horasPorDia = 8;

        return $diasFormacion * $horasPorDia;
    }
}
