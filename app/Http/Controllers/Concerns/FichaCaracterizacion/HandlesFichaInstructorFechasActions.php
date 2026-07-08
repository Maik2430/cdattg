<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\InstructorFichaCaracterizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesFichaInstructorFechasActions
{
    /**
     * Verifica si un instructor tiene asignaciones superpuestas en un rango de fechas.
     *
     * @param  int  $instructorId
     * @param  string  $fechaInicio
     * @param  string  $fechaFin
     * @param  int|null  $excludeInstructorFichaId  (opcional, para excluir una asignación específica al editar)
     * @return array
     */
    public function verificarConflictosFechasInstructor($instructorId, $fechaInicio, $fechaFin, $excludeInstructorFichaId = null)
    {
        try {
            $query = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
                ->where(function ($q) use ($fechaInicio, $fechaFin) {
                    // Verificar superposición de rangos de fechas
                    $q->where(function ($subQ) use ($fechaInicio) {
                        // La nueva fecha de inicio está dentro de un rango existente
                        $subQ->where('fecha_inicio', '<=', $fechaInicio)
                            ->where('fecha_fin', '>=', $fechaInicio);
                    })->orWhere(function ($subQ) use ($fechaFin) {
                        // La nueva fecha de fin está dentro de un rango existente
                        $subQ->where('fecha_inicio', '<=', $fechaFin)
                            ->where('fecha_fin', '>=', $fechaFin);
                    })->orWhere(function ($subQ) use ($fechaInicio, $fechaFin) {
                        // El nuevo rango contiene completamente un rango existente
                        $subQ->where('fecha_inicio', '>=', $fechaInicio)
                            ->where('fecha_fin', '<=', $fechaFin);
                    });
                });

            // Excluir una asignación específica si se está editando
            if ($excludeInstructorFichaId) {
                $query->where('id', '!=', $excludeInstructorFichaId);
            }

            $conflictos = $query->with(['fichaCaracterizacion.programaFormacion'])->get();

            return [
                'tiene_conflictos' => $conflictos->count() > 0,
                'conflictos' => $conflictos->map(function ($conflicto) {
                    return [
                        'id' => $conflicto->id,
                        'fecha_inicio' => $conflicto->fecha_inicio ? \Carbon\Carbon::parse($conflicto->fecha_inicio)->format('d/m/Y') : 'N/A',
                        'fecha_fin' => $conflicto->fecha_fin ? \Carbon\Carbon::parse($conflicto->fecha_fin)->format('d/m/Y') : 'N/A',
                        'ficha' => $conflicto->fichaCaracterizacion->ficha,
                        'programa' => $conflicto->fichaCaracterizacion->programaFormacion->nombre ?? 'Sin programa',
                        'total_horas' => $conflicto->total_horas_instructor,
                    ];
                }),
            ];

        } catch (\Exception $e) {
            Log::error('Error al verificar conflictos de fechas del instructor', [
                'instructor_id' => $instructorId,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'error' => $e->getMessage(),
            ]);

            return [
                'tiene_conflictos' => true,
                'conflictos' => [],
                'error' => 'Error al verificar disponibilidad de fechas',
            ];
        }
    }

    /**
     * API endpoint para verificar conflictos de fechas de un instructor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verificarDisponibilidadFechasInstructor(Request $request)
    {
        try {
            $request->validate([
                'instructor_id' => 'required|exists:instructors,id',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'exclude_instructor_ficha_id' => 'nullable|integer',
            ]);

            $resultado = $this->verificarConflictosFechasInstructor(
                $request->instructor_id,
                $request->fecha_inicio,
                $request->fecha_fin,
                $request->exclude_instructor_ficha_id
            );

            return response()->json([
                'disponible' => ! $resultado['tiene_conflictos'],
                'conflictos' => $resultado['conflictos'],
                'mensaje' => $resultado['tiene_conflictos']
                    ? 'El instructor tiene asignaciones superpuestas en ese rango de fechas'
                    : 'El instructor está disponible en ese rango de fechas',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de entrada inválidos',
                'details' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en verificación de disponibilidad de fechas', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'error' => 'Error interno del servidor',
            ], 500);
        }
    }
}
