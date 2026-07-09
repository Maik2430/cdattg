<?php

namespace App\Services\Concerns\FichaCaracterizacionValidation;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Carbon\Carbon;

trait HandlesFichaCaracterizacionValidationAdicionalesPeriodoHelpers
{
    /**
     * @param  int|null  $excluirFichaId
     * @return array{errores: array<int, string>, advertencias: array<int, string>}
     */
    private function validarPeriodoYDisponibilidadAdicionales(array $datos, $excluirFichaId = null): array
    {
        $errores = [];
        $advertencias = [];

        if (isset($datos['modalidad_formacion_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
            $duracionDias = Carbon::parse($datos['fecha_inicio'])->diffInDays(Carbon::parse($datos['fecha_fin']));

            $duracionesMinimas = [
                1 => 30,
                2 => 60,
                3 => 90,
            ];

            $duracionMinima = $duracionesMinimas[$datos['modalidad_formacion_id']] ?? 30;

            if ($duracionDias < $duracionMinima) {
                $advertencias[] = "La duración del programa es menor a lo recomendado para esta modalidad ({$duracionMinima} días mínimo).";
            }
        }

        if (isset($datos['programa_formacion_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
            $programasSimilares = FichaCaracterizacion::where('programa_formacion_id', $datos['programa_formacion_id'])
                ->where('status', true)
                ->where('id', '!=', $excluirFichaId)
                ->where(function ($q) use ($datos) {
                    $q->whereBetween('fecha_inicio', [$datos['fecha_inicio'], $datos['fecha_fin']])
                        ->orWhereBetween('fecha_fin', [$datos['fecha_inicio'], $datos['fecha_fin']])
                        ->orWhere(function ($subQuery) use ($datos) {
                            $subQuery->where('fecha_inicio', '<=', $datos['fecha_inicio'])
                                ->where('fecha_fin', '>=', $datos['fecha_fin']);
                        });
                })
                ->count();

            if ($programasSimilares > 0) {
                $advertencias[] = 'Ya existen programas similares en las fechas seleccionadas. Se recomienda verificar la disponibilidad.';
            }
        }

        if (isset($datos['instructor_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
            $fichasInstructor = FichaCaracterizacion::where('instructor_id', $datos['instructor_id'])
                ->where('status', true)
                ->where('id', '!=', $excluirFichaId)
                ->where(function ($q) use ($datos) {
                    $q->whereBetween('fecha_inicio', [$datos['fecha_inicio'], $datos['fecha_fin']])
                        ->orWhereBetween('fecha_fin', [$datos['fecha_inicio'], $datos['fecha_fin']]);
                })
                ->count();

            if ($fichasInstructor >= 2) {
                $advertencias[] = 'El instructor ya tiene múltiples fichas asignadas en el período seleccionado.';
            }
        }

        if (isset($datos['fecha_inicio'])) {
            $fechaInicio = Carbon::parse($datos['fecha_inicio']);
            $fechaActual = Carbon::now();
            $diferenciaMeses = $fechaActual->diffInMonths($fechaInicio);

            if ($diferenciaMeses > 12) {
                $advertencias[] = 'La fecha de inicio está muy lejana en el futuro (más de 12 meses).';
            }
        }

        if (isset($datos['programa_formacion_id'])) {
            $instructoresDisponibles = Instructor::where('status', true)
                ->whereHas('competencias', function ($q) use ($datos) {
                    $q->whereHas('programas', function ($subQ) use ($datos) {
                        $subQ->where('programa_id', $datos['programa_formacion_id']);
                    });
                })
                ->count();

            if ($instructoresDisponibles === 0) {
                $advertencias[] = 'No se encontraron instructores con las competencias requeridas para este programa.';
            }
        }

        return [
            'errores' => $errores,
            'advertencias' => $advertencias,
        ];
    }
}
