<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Carbon\Carbon;

trait HandlesAsignarInstructoresInstructorFieldValidationHelpers
{
    private function validarInstructorActivo($instructorId, $fail): void
    {
        $instructor = Instructor::find($instructorId);
        if ($instructor && ! $instructor->status) {
            $fail("El instructor {$instructor->nombre_completo} está inactivo.");
        }
    }

    private function validarLimiteFichasActivas($instructorId, $fail): void
    {
        $instructor = Instructor::find($instructorId);
        if (! $instructor) {
            return;
        }

        $fichasActivas = $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) {
                $q->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->count();

        if ($fichasActivas >= 5) {
            $fail("El instructor {$instructor->nombre_completo} ya tiene el máximo de fichas activas (5).");
        }
    }

    private function validarFechaInicioFicha($fechaInicio, $fail): void
    {
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::find($fichaId);

        if ($ficha && $ficha->fecha_inicio) {
            $fechaInicioFicha = Carbon::parse($ficha->fecha_inicio);
            $fechaInicioInstructor = Carbon::parse($fechaInicio);

            if ($fechaInicioInstructor->lt($fechaInicioFicha)) {
                $fail("La fecha de inicio del instructor debe ser posterior o igual a la fecha de inicio de la ficha ({$fechaInicioFicha->format('d/m/Y')}).");
            }
        }
    }

    private function validarFechaFinFicha($fechaFin, $fail): void
    {
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::find($fichaId);

        if ($ficha && $ficha->fecha_fin) {
            $fechaFinFicha = Carbon::parse($ficha->fecha_fin);
            $fechaFinInstructor = Carbon::parse($fechaFin);

            if ($fechaFinInstructor->gt($fechaFinFicha)) {
                $fail("La fecha de fin del instructor debe ser anterior o igual a la fecha de fin de la ficha ({$fechaFinFicha->format('d/m/Y')}).");
            }
        }
    }

    private function validarInstructorPrincipalEnLista($instructorPrincipalId, $fail): void {}
}
