<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

use App\Models\Instructor;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\Parametro;
use Carbon\Carbon;

trait HandlesAsignarInstructoresConflictAsignacionesExistentesHelpers
{
    private function validarConflictosConAsignacionesExistentes($validator, $instructorIdActual, $fechaInicioActual, $fechaFinActual, $diasActuales, $indexActual, $fichaId): void
    {
        $asignacionesExistentes = InstructorFichaCaracterizacion::where('ficha_id', $fichaId)
            ->where('instructor_id', '!=', $instructorIdActual)
            ->with(['instructor.persona', 'instructorFichaDias.dia'])
            ->get();

        \Log::info('🔍 ASIGNACIONES EXISTENTES EN FICHA', [
            'ficha_id' => $fichaId,
            'total_existentes' => $asignacionesExistentes->count(),
            'asignaciones' => $asignacionesExistentes->map(function ($a) {
                return [
                    'instructor_id' => $a->instructor_id,
                    'instructor_nombre' => $a->instructor->nombre_completo ?? 'Sin nombre',
                    'fecha_inicio' => $a->fecha_inicio,
                    'fecha_fin' => $a->fecha_fin,
                    'dias' => $a->instructorFichaDias->pluck('dia_id')->toArray(),
                ];
            })->toArray(),
        ]);

        foreach ($asignacionesExistentes as $asignacionExistente) {
            $instructorIdExistente = $asignacionExistente->instructor_id;
            $fechaInicioExistente = Carbon::parse($asignacionExistente->fecha_inicio);
            $fechaFinExistente = Carbon::parse($asignacionExistente->fecha_fin);
            $diasExistentes = $asignacionExistente->instructorFichaDias->pluck('dia_id')->toArray();

            \Log::info('🔍 COMPARANDO CON ASIGNACIÓN EXISTENTE', [
                'instructor_existente' => $instructorIdExistente,
                'fecha_existente' => $fechaInicioExistente->format('Y-m-d').' a '.$fechaFinExistente->format('Y-m-d'),
                'dias_existentes' => $diasExistentes,
            ]);

            $haySuperposicion = $this->haySuperposicionFechas($fechaInicioActual, $fechaFinActual, $fechaInicioExistente, $fechaFinExistente);

            \Log::info('🔍 SUPERPOSICIÓN CON EXISTENTE', [
                'hay_superposicion' => $haySuperposicion,
            ]);

            if ($haySuperposicion) {
                $diasEnComun = array_intersect($diasActuales, $diasExistentes);

                \Log::info('🔍 DÍAS EN COMÚN CON EXISTENTE', [
                    'dias_en_comun' => $diasEnComun,
                    'hay_conflicto' => ! empty($diasEnComun),
                ]);

                if (! empty($diasEnComun)) {
                    $instructorActual = Instructor::find($instructorIdActual);
                    $instructorExistente = Instructor::find($instructorIdExistente);
                    $diasNombres = Parametro::whereIn('id', $diasEnComun)->pluck('name')->implode(', ');

                    \Log::error('❌ CONFLICTO CON ASIGNACIÓN EXISTENTE', [
                        'instructor_actual' => $instructorActual->nombre_completo,
                        'instructor_existente' => $instructorExistente->nombre_completo,
                        'dias_conflicto' => $diasNombres,
                    ]);

                    $validator->errors()->add(
                        "instructores.{$indexActual}.fecha_inicio",
                        "⚠️ CONFLICTO CON INSTRUCTOR YA ASIGNADO: El instructor {$instructorActual->nombre_completo} no puede ser asignado en las mismas fechas y días ({$diasNombres}) que el instructor {$instructorExistente->nombre_completo} que ya está asignado a esta ficha. Ajuste las fechas o días para evitar el conflicto."
                    );
                }
            }
        }
    }
}
