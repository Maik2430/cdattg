<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use Illuminate\Http\Request;

trait HandlesFichaInstructorAsignacionValidationHelpers
{
    private function prepareInstructorDataForAsignacionUpdate(Request $request, $instructorFicha): array
    {
        $instructorData = [
            'instructor_id' => $instructorFicha->instructor_id,
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
            'competencia_id' => $request->input('competencia_id'),
            'resultados_aprendizaje' => $request->input('resultados_aprendizaje', []),
            'dias' => $request->input('dias', []),
        ];

        $diasFormato = [];
        if (is_array($instructorData['dias'])) {
            foreach ($instructorData['dias'] as $key => $dia) {
                if (is_array($dia)) {
                    if (is_numeric($key)) {
                        $diasFormato[$key] = [
                            'hora_inicio' => $dia['hora_inicio'] ?? null,
                            'hora_fin' => $dia['hora_fin'] ?? null,
                        ];
                    } elseif (isset($dia['dia_id'])) {
                        $diasFormato[$dia['dia_id']] = [
                            'hora_inicio' => $dia['hora_inicio'] ?? null,
                            'hora_fin' => $dia['hora_fin'] ?? null,
                        ];
                    }
                }
            }
        }
        $instructorData['dias'] = $diasFormato;

        return $instructorData;
    }

    private function validateActualizarAsignacionInstructor(array $instructorData, string $fichaId)
    {
        $validator = \Validator::make([
            'instructores' => [$instructorData],
        ], [
            'instructores' => 'required|array|min:1',
            'instructores.0.instructor_id' => 'required|integer|exists:instructors,id',
            'instructores.0.fecha_inicio' => 'required|date|after_or_equal:today',
            'instructores.0.fecha_fin' => 'required|date|after_or_equal:instructores.0.fecha_inicio',
            'instructores.0.competencia_id' => 'nullable|integer|exists:competencias,id',
            'instructores.0.resultados_aprendizaje' => 'nullable|array',
            'instructores.0.resultados_aprendizaje.*' => 'required|integer|exists:resultados_aprendizajes,id',
            'instructores.0.dias' => 'required|array|min:1',
        ]);

        $validator->after(function ($validator) use ($instructorData, $fichaId) {
            $ficha = \App\Models\FichaCaracterizacion::find($fichaId);

            if ($ficha && $ficha->fecha_inicio) {
                $fechaInicioFicha = \Carbon\Carbon::parse($ficha->fecha_inicio);
                $fechaInicioInstructor = \Carbon\Carbon::parse($instructorData['fecha_inicio']);

                if ($fechaInicioInstructor->lt($fechaInicioFicha)) {
                    $validator->errors()->add('instructores.0.fecha_inicio',
                        "La fecha de inicio del instructor debe ser posterior o igual a la fecha de inicio de la ficha ({$fechaInicioFicha->format('d/m/Y')}).");
                }
            }

            if ($ficha && $ficha->fecha_fin) {
                $fechaFinFicha = \Carbon\Carbon::parse($ficha->fecha_fin);
                $fechaFinInstructor = \Carbon\Carbon::parse($instructorData['fecha_fin']);

                if ($fechaFinInstructor->gt($fechaFinFicha)) {
                    $validator->errors()->add('instructores.0.fecha_fin',
                        "La fecha de fin del instructor debe ser anterior o igual a la fecha de fin de la ficha ({$fechaFinFicha->format('d/m/Y')}).");
                }
            }

            if ($instructorData['competencia_id'] && $ficha && $ficha->programaFormacion) {
                $competenciaPertenece = $ficha->programaFormacion->competencias->contains('id', $instructorData['competencia_id']);
                if (! $competenciaPertenece) {
                    $competencia = \App\Models\Competencia::find($instructorData['competencia_id']);
                    $competenciaNombre = $competencia ? $competencia->nombre : 'Competencia desconocida';
                    $validator->errors()->add('instructores.0.competencia_id',
                        "La competencia '{$competenciaNombre}' no pertenece al programa de formación de esta ficha.");
                }
            }

            if (! empty($instructorData['resultados_aprendizaje']) && $instructorData['competencia_id']) {
                $competencia = \App\Models\Competencia::with('resultadosAprendizaje')->find($instructorData['competencia_id']);
                if ($competencia) {
                    foreach ($instructorData['resultados_aprendizaje'] as $resultadoId) {
                        $resultadoPertenece = $competencia->resultadosAprendizaje->contains('id', $resultadoId);
                        if (! $resultadoPertenece) {
                            $resultado = \App\Models\ResultadosAprendizaje::find($resultadoId);
                            $resultadoNombre = $resultado ? $resultado->nombre : 'Resultado desconocido';
                            $validator->errors()->add('instructores.0.resultados_aprendizaje',
                                "El resultado de aprendizaje '{$resultadoNombre}' no pertenece a la competencia seleccionada.");
                        }
                    }
                }
            }
        });

        return $validator;
    }
}
