<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorFichaUnassignActions
{
    /**
     * Desasignar ficha del instructor
     */
    public function desasignarFicha(Request $request, Instructor $instructor, $fichaId)
    {
        try {
            $instructorFicha = $instructor->instructorFichas()
                ->where('ficha_caracterizacion_id', $fichaId)
                ->first();

            if (! $instructorFicha) {
                return response()->json([
                    'success' => false,
                    'message' => 'La ficha no está asignada a este instructor',
                ], 404);
            }

            // Verificar que la ficha no haya comenzado aún
            if (Carbon::parse($instructorFicha->fecha_inicio)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede desasignar una ficha que ya ha comenzado',
                ], 400);
            }

            $instructorFicha->update([
                'status' => false,
                'user_edit_id' => Auth::id(),
            ]);

            Log::info('Ficha desasignada del instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $fichaId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ficha desasignada exitosamente del instructor',
            ]);
        } catch (Exception $e) {
            Log::error('Error desasignando ficha del instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al desasignar la ficha del instructor',
            ], 500);
        }
    }
}
