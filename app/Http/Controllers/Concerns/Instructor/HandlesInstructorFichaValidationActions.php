<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorFichaValidationActions
{
    /**
     * Validar reglas SENA para asignación de ficha
     */
    public function validarReglasSENA(Request $request, Instructor $instructor)
    {
        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'especialidad_requerida' => 'nullable|string',
                'regional_id' => 'nullable|integer|exists:regionals,id',
            ]);

            $datosFicha = $request->only(['fecha_inicio', 'fecha_fin', 'especialidad_requerida', 'regional_id']);
            $validacion = $this->businessRulesService->validarReglasSENA($instructor, $datosFicha);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'validacion' => $validacion,
                ]);
            }

            return response()->json($validacion);
        } catch (Exception $e) {
            Log::error('Error validando reglas SENA', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al validar reglas de negocio',
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al validar reglas de negocio');
        }
    }

    /**
     * Obtener estadísticas de carga de trabajo
     */
    public function estadisticasCargaTrabajo(Request $request)
    {
        try {
            $estadisticas = $this->businessRulesService->obtenerEstadisticasCargaTrabajo();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'estadisticas' => $estadisticas,
                ]);
            }

            return view('instructores.estadisticas-carga', compact('estadisticas'));
        } catch (Exception $e) {
            Log::error('Error obteniendo estadísticas de carga', [
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener estadísticas',
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al obtener estadísticas de carga de trabajo');
        }
    }
}
