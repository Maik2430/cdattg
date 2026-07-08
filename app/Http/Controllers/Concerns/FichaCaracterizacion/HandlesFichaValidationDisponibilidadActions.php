<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use App\Models\InstructorFichaCaracterizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesFichaValidationDisponibilidadActions
{
    /**
     * Valida la disponibilidad de un ambiente en un rango de fechas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validarDisponibilidadAmbiente(Request $request)
    {
        try {
            $request->validate([
                'ambiente_id' => 'required|integer|exists:ambientes,id',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'excluir_ficha_id' => 'nullable|integer',
            ]);

            // Validar disponibilidad del ambiente directamente
            $ambientesOcupados = FichaCaracterizacion::where('ambiente_id', $request->ambiente_id)
                ->where('status', 1)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
                        ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $request->fecha_fin])
                        ->orWhere(function ($subQuery) use ($request) {
                            $subQuery->where('fecha_inicio', '<=', $request->fecha_inicio)
                                ->where('fecha_fin', '>=', $request->fecha_fin);
                        });
                });

            if ($request->excluir_ficha_id) {
                $ambientesOcupados->where('id', '!=', $request->excluir_ficha_id);
            }

            $resultado = [
                'valido' => ! $ambientesOcupados->exists(),
                'mensaje' => $ambientesOcupados->exists()
                    ? 'El ambiente no está disponible en el rango de fechas especificado'
                    : 'El ambiente está disponible',
            ];

            return response()->json($resultado);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Error de validación en los datos enviados.',
                'errores' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al validar disponibilidad de ambiente', [
                'error' => $e->getMessage(),
                'datos' => $request->all(),
            ]);

            return response()->json([
                'valido' => false,
                'mensaje' => 'Error interno al validar disponibilidad del ambiente.',
            ], 500);
        }
    }

    /**
     * Valida la disponibilidad de un instructor en un rango de fechas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validarDisponibilidadInstructor(Request $request)
    {
        try {
            $request->validate([
                'instructor_id' => 'required|integer|exists:instructors,id',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'excluir_ficha_id' => 'nullable|integer',
            ]);

            // Validar disponibilidad del instructor directamente
            $instructoresOcupados = InstructorFichaCaracterizacion::where('instructor_id', $request->instructor_id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
                        ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $request->fecha_fin])
                        ->orWhere(function ($subQuery) use ($request) {
                            $subQuery->where('fecha_inicio', '<=', $request->fecha_inicio)
                                ->where('fecha_fin', '>=', $request->fecha_fin);
                        });
                });

            if ($request->excluir_ficha_id) {
                $instructoresOcupados->where('ficha_id', '!=', $request->excluir_ficha_id);
            }

            $resultado = [
                'valido' => ! $instructoresOcupados->exists(),
                'mensaje' => $instructoresOcupados->exists()
                    ? 'El instructor no está disponible en el rango de fechas especificado'
                    : 'El instructor está disponible',
            ];

            return response()->json($resultado);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Error de validación en los datos enviados.',
                'errores' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al validar disponibilidad de instructor', [
                'error' => $e->getMessage(),
                'datos' => $request->all(),
            ]);

            return response()->json([
                'valido' => false,
                'mensaje' => 'Error interno al validar disponibilidad del instructor.',
            ], 500);
        }
    }
}
