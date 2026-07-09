<?php

namespace App\Services\Concerns\InstructorBusinessRules;

use App\Models\Instructor;
use Carbon\Carbon;

trait HandlesInstructorBusinessRulesFichasContadoresHelpers
{
    public function contarFichasActivas(Instructor $instructor): int
    {
        return $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) {
                $q->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->count();
    }

    public function contarFichasFinalizadas(Instructor $instructor): int
    {
        return $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) {
                $q->where('fecha_fin', '<', now()->toDateString());
            })
            ->count();
    }

    public function contarTotalFichasAsignadas(Instructor $instructor): int
    {
        return $instructor->instructorFichas()->count();
    }

    public function contarFichasProximas(Instructor $instructor, int $dias = 30): int
    {
        return $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) use ($dias) {
                $q->where('status', true)
                    ->where('fecha_inicio', '>=', now()->toDateString())
                    ->where('fecha_inicio', '<=', now()->addDays($dias)->toDateString());
            })
            ->count();
    }

    public function sumarTotalHorasInstructor(Instructor $instructor, bool $soloActivas = false): int
    {
        $query = $instructor->instructorFichas();

        if ($soloActivas) {
            $query->whereHas('ficha', function ($q) {
                $q->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            });
        }

        return (int) $query->sum('total_horas_instructor');
    }

    public function sumarHorasDelMes(Instructor $instructor, Carbon $fechaReferencia): int
    {
        return (int) $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) use ($fechaReferencia) {
                $q->whereMonth('fecha_inicio', $fechaReferencia->month)
                    ->whereYear('fecha_inicio', $fechaReferencia->year);
            })
            ->sum('total_horas_instructor');
    }

    public function promedioHorasUltimosMeses(Instructor $instructor, int $cantidadMeses = 6): float
    {
        if ($cantidadMeses <= 0) {
            return 0.0;
        }

        $totalHoras = $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) use ($cantidadMeses) {
                $q->where('fecha_inicio', '>=', now()->subMonths($cantidadMeses)->toDateString());
            })
            ->sum('total_horas_instructor');

        return $totalHoras / $cantidadMeses;
    }

    public function obtenerResumenFichas(Instructor $instructor): array
    {
        return [
            'total' => $instructor->instructorFichas()->count(),
            'activas' => $this->contarFichasActivas($instructor),
            'finalizadas' => $this->contarFichasFinalizadas($instructor),
            'proximas' => $this->contarFichasProximas($instructor),
            'total_horas' => $this->sumarTotalHorasInstructor($instructor),
        ];
    }
}
