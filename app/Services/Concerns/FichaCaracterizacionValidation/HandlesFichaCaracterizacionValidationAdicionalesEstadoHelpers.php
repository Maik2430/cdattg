<?php

namespace App\Services\Concerns\FichaCaracterizacionValidation;

use App\Models\Ambiente;
use App\Models\Instructor;
use App\Models\ProgramaFormacion;
use App\Models\Sede;

trait HandlesFichaCaracterizacionValidationAdicionalesEstadoHelpers
{
    /**
     * @return array{errores: array<int, string>}
     */
    private function validarEntidadesActivasAdicionales(array $datos): array
    {
        $errores = [];

        if (isset($datos['programa_formacion_id'])) {
            $programa = ProgramaFormacion::find($datos['programa_formacion_id']);
            if ($programa && isset($programa->status) && ! $programa->status) {
                $errores[] = 'El programa de formación seleccionado está inactivo.';
            }
        }

        if (isset($datos['sede_id'])) {
            $sede = Sede::find($datos['sede_id']);
            if ($sede && isset($sede->status) && ! $sede->status) {
                $errores[] = 'La sede seleccionada está inactiva.';
            }
        }

        if (isset($datos['ambiente_id'])) {
            $ambiente = Ambiente::find($datos['ambiente_id']);
            if ($ambiente && isset($ambiente->status) && ! $ambiente->status) {
                $errores[] = 'El ambiente seleccionado está inactivo.';
            }
        }

        if (isset($datos['instructor_id'])) {
            $instructor = Instructor::find($datos['instructor_id']);
            if ($instructor && isset($instructor->status) && ! $instructor->status) {
                $errores[] = 'El instructor seleccionado está inactivo.';
            }
        }

        if (isset($datos['ambiente_id'])) {
            $ambiente = Ambiente::find($datos['ambiente_id']);
            if ($ambiente && isset($ambiente->estado) && $ambiente->estado === 'MANTENIMIENTO') {
                $errores[] = 'El ambiente seleccionado está en mantenimiento y no está disponible.';
            }
        }

        return ['errores' => $errores];
    }
}
