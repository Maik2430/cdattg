<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

use App\Models\Instructor;
use App\Models\Parametro;
use Carbon\Carbon;

trait HandlesAsignarInstructoresConflictMismaFichaFormularioHelpers
{
    private function validarConflictosMismaFicha($validator, $instructores, $indexActual, $instructorIdActual, $fechaInicioActual, $fechaFinActual, $diasActuales): void
    {
        $fichaId = $this->route('id');

        \Log::info('🔍 VALIDACIÓN MISMA FICHA', [
            'instructores_total' => count($instructores),
            'index_actual' => $indexActual,
            'instructor_actual' => $instructorIdActual,
            'fecha_actual' => $fechaInicioActual->format('Y-m-d').' a '.$fechaFinActual->format('Y-m-d'),
            'dias_actuales' => $diasActuales,
            'ficha_id' => $fichaId,
        ]);

        foreach ($instructores as $indexOtro => $instructorOtro) {
            if ($indexActual === $indexOtro) {
                continue;
            }

            $instructorIdOtro = $instructorOtro['instructor_id'];
            $fechaInicioOtro = Carbon::parse($instructorOtro['fecha_inicio']);
            $fechaFinOtro = Carbon::parse($instructorOtro['fecha_fin']);

            $diasOtros = [];
            if (isset($instructorOtro['dias']) && is_array($instructorOtro['dias'])) {
                $diasOtros = array_keys($instructorOtro['dias']);
            } elseif (isset($instructorOtro['dias_semana']) && is_array($instructorOtro['dias_semana'])) {
                $diasOtros = $instructorOtro['dias_semana'];
            } elseif (isset($instructorOtro['dias_formacion']) && is_array($instructorOtro['dias_formacion'])) {
                $diasOtros = collect($instructorOtro['dias_formacion'])->pluck('dia_id')->filter()->toArray();
            }

            \Log::info('🔍 COMPARANDO CON INSTRUCTOR EN FORMULARIO', [
                'index_otro' => $indexOtro,
                'instructor_otro' => $instructorIdOtro,
                'fecha_otro' => $fechaInicioOtro->format('Y-m-d').' a '.$fechaFinOtro->format('Y-m-d'),
                'dias_otros' => $diasOtros,
            ]);

            $haySuperposicion = $this->haySuperposicionFechas($fechaInicioActual, $fechaFinActual, $fechaInicioOtro, $fechaFinOtro);

            \Log::info('🔍 SUPERPOSICIÓN DE FECHAS', [
                'hay_superposicion' => $haySuperposicion,
            ]);

            if ($haySuperposicion) {
                $diasEnComun = array_intersect($diasActuales, $diasOtros);

                \Log::info('🔍 DÍAS EN COMÚN', [
                    'dias_en_comun' => $diasEnComun,
                    'hay_conflicto' => ! empty($diasEnComun),
                ]);

                if (! empty($diasEnComun)) {
                    $instructorActual = Instructor::find($instructorIdActual);
                    $instructorOtroModel = Instructor::find($instructorIdOtro);
                    $diasNombres = Parametro::whereIn('id', $diasEnComun)->pluck('name')->implode(', ');

                    \Log::error('❌ CONFLICTO DETECTADO EN FORMULARIO', [
                        'instructor_actual' => $instructorActual->nombre_completo,
                        'instructor_otro' => $instructorOtroModel->nombre_completo,
                        'dias_conflicto' => $diasNombres,
                    ]);

                    $validator->errors()->add(
                        "instructores.{$indexActual}.fecha_inicio",
                        "⚠️ CONFLICTO EN LA MISMA FICHA: El instructor {$instructorActual->nombre_completo} no puede ser asignado en las mismas fechas y días ({$diasNombres}) que el instructor {$instructorOtroModel->nombre_completo}. Ajuste las fechas o días para evitar el conflicto."
                    );
                }
            }
        }

        $this->validarConflictosConAsignacionesExistentes($validator, $instructorIdActual, $fechaInicioActual, $fechaFinActual, $diasActuales, $indexActual, $fichaId);
    }

    private function haySuperposicionFechas($fechaInicio1, $fechaFin1, $fechaInicio2, $fechaFin2): bool
    {
        return $fechaInicio1->lte($fechaFin2) && $fechaFin1->gte($fechaInicio2);
    }
}
