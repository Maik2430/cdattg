<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorFichaAssignActions
{
    /**
     * Asignar ficha a instructor con validaciones de negocio
     */
    public function asignarFicha(Request $request, Instructor $instructor)
    {
        try {
            $request->validate([
                'ficha_id' => 'required|integer|exists:ficha_caracterizacions,id',
                'total_horas_instructor' => 'required|integer|min:1|max:1000',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            ]);

            $ficha = FichaCaracterizacion::findOrFail($request->ficha_id);

            // Verificar que la ficha esté activa
            if (! $ficha->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'La ficha seleccionada no está activa',
                ], 400);
            }

            // Validar disponibilidad del instructor
            $datosFicha = [
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'especialidad_requerida' => $ficha->programaFormacion->redConocimiento->nombre ?? null,
                'regional_id' => $ficha->regional_id,
                'horas_semanales' => $request->total_horas_instructor,
            ];

            $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha);

            if (! $disponibilidad['disponible']) {
                return response()->json([
                    'success' => false,
                    'message' => 'El instructor no está disponible para esta ficha',
                    'razones' => $disponibilidad['razones'],
                    'conflictos' => $disponibilidad['conflictos'] ?? [],
                ], 400);
            }

            // Validar reglas SENA
            $validacionSENA = $this->businessRulesService->validarReglasSENA($instructor, $datosFicha);

            if (! $validacionSENA['valido']) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se cumplen las reglas de negocio del SENA',
                    'errores' => $validacionSENA['errores'],
                ], 400);
            }

            // Crear la asignación
            $instructorFicha = $instructor->instructorFichas()->create([
                'ficha_caracterizacion_id' => $ficha->id,
                'total_horas_instructor' => $request->total_horas_instructor,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'status' => true,
                'user_create_id' => Auth::id(),
            ]);

            Log::info('Ficha asignada al instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $ficha->id,
                'total_horas' => $request->total_horas_instructor,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ficha asignada exitosamente al instructor',
                'asignacion' => $instructorFicha,
                'advertencias' => $validacionSENA['advertencias'] ?? [],
            ]);
        } catch (Exception $e) {
            Log::error('Error asignando ficha al instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $request->ficha_id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al asignar la ficha al instructor',
            ], 500);
        }
    }
}
