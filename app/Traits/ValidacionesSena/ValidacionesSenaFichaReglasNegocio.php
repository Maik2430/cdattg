<?php

namespace App\Traits\ValidacionesSena;

use Carbon\Carbon;

trait ValidacionesSenaFichaReglasNegocio
{
    /**
     * Valida las reglas de negocio específicas del SENA.
     *
     * @param  array  $datos  Datos de la ficha
     * @param  int|null  $excluirFichaId  ID de ficha a excluir (para actualizaciones)
     * @return array Resultado de la validación
     */
    protected function validarReglasNegocioSena($datos, $excluirFichaId = null)
    {
        $errores = [];

        try {
            // 1. Validar que las fechas sean laborales (Lunes a Viernes)
            if (isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $fechaInicio = Carbon::parse($datos['fecha_inicio']);
                $fechaFin = Carbon::parse($datos['fecha_fin']);

                // Verificar que no sean fines de semana
                if ($fechaInicio->isWeekend()) {
                    $errores[] = 'La fecha de inicio no puede ser un fin de semana (sábado o domingo).';
                }

                if ($fechaFin->isWeekend()) {
                    $errores[] = 'La fecha de fin no puede ser un fin de semana (sábado o domingo).';
                }

                // Verificar que la duración mínima sea de 1 mes
                $duracionDias = $fechaInicio->diffInDays($fechaFin);
                if ($duracionDias < 30) {
                    $errores[] = 'La duración mínima de un programa debe ser de 30 días.';
                }

                // Verificar que la duración máxima no exceda 2 años
                if ($duracionDias > 730) {
                    $errores[] = 'La duración máxima de un programa no puede exceder 2 años (730 días).';
                }
            }

            // 2. Validar horarios según jornada
            if (isset($datos['jornada_id']) && isset($datos['fecha_inicio'])) {
                $validacionJornada = $this->validarHorariosSegunJornada($datos['jornada_id'], $datos['fecha_inicio']);
                if (! $validacionJornada['valido']) {
                    $errores[] = $validacionJornada['mensaje'];
                }
            }

            // 3. Validar que el ambiente pertenezca a la misma sede del programa
            if (isset($datos['ambiente_id']) && isset($datos['sede_id'])) {
                $validacionAmbienteSede = $this->validarAmbientePerteneceASede($datos['ambiente_id'], $datos['sede_id']);
                if (! $validacionAmbienteSede['valido']) {
                    $errores[] = $validacionAmbienteSede['mensaje'];
                }
            }

            // 4. Validar que el instructor pertenezca a la misma regional
            if (isset($datos['instructor_id']) && isset($datos['sede_id'])) {
                $validacionInstructorRegional = $this->validarInstructorPerteneceARegional($datos['instructor_id'], $datos['sede_id']);
                if (! $validacionInstructorRegional['valido']) {
                    $errores[] = $validacionInstructorRegional['mensaje'];
                }
            }

            // 5. Validar límite de aprendices por ficha según programa
            // Solo validar si se está actualizando una ficha existente (tiene excluirFichaId)
            if ($excluirFichaId !== null && isset($datos['programa_formacion_id'])) {
                $validacionLimiteAprendices = $this->validarLimiteAprendicesPorFicha($excluirFichaId, $datos['programa_formacion_id']);
                if (! $validacionLimiteAprendices['valido']) {
                    $errores[] = $validacionLimiteAprendices['mensaje'];
                }
            }

            // 6. Validar días festivos
            // COMENTADO: La validación de días festivos se deshabilita según solicitud del usuario
            // if (isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
            //     $validacionFestivos = $this->validarFechasFestivos($datos['fecha_inicio'], $datos['fecha_fin']);
            //     if (!$validacionFestivos['valido']) {
            //         $errores[] = $validacionFestivos['mensaje'];
            //     }
            // }

            // 7. Validar capacidad del ambiente
            if (isset($datos['ambiente_id']) && isset($datos['programa_formacion_id'])) {
                $validacionCapacidad = $this->validarCapacidadAmbiente($datos['ambiente_id'], $datos['programa_formacion_id']);
                if (! $validacionCapacidad['valido']) {
                    $errores[] = $validacionCapacidad['mensaje'];
                }
            }

            // 8. Validar competencias del instructor
            if (isset($datos['instructor_id']) && isset($datos['programa_formacion_id'])) {
                $validacionCompetencias = $this->validarCompetenciasInstructor($datos['instructor_id'], $datos['programa_formacion_id']);
                if (! $validacionCompetencias['valido']) {
                    $errores[] = $validacionCompetencias['mensaje'];
                }
            }

            // 9. Validar límite de fichas por instructor
            if (isset($datos['instructor_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $validacionLimiteFichas = $this->validarLimiteFichasPorInstructor(
                    $datos['instructor_id'],
                    $datos['fecha_inicio'],
                    $datos['fecha_fin'],
                    $excluirFichaId
                );
                if (! $validacionLimiteFichas['valido']) {
                    $errores[] = $validacionLimiteFichas['mensaje'];
                }
            }

            // 10. Validar superposición de horarios en ambiente
            if (isset($datos['ambiente_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $validacionSuperposicion = $this->validarSuperposicionHorariosAmbiente(
                    $datos['ambiente_id'],
                    $datos['fecha_inicio'],
                    $datos['fecha_fin'],
                    $excluirFichaId
                );
                if (! $validacionSuperposicion['valido']) {
                    $errores[] = $validacionSuperposicion['mensaje'];
                }
            }

            if (! empty($errores)) {
                return [
                    'valido' => false,
                    'mensaje' => implode(' ', $errores),
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'Todas las reglas de negocio se cumplen correctamente.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar reglas de negocio: '.$e->getMessage(),
            ];
        }
    }
}
