<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Http\Requests\InstructoresDisponiblesRequest;
use App\Http\Requests\VerificarDisponibilidadRequest;
use App\Models\Instructor;
use Exception;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorFichaAvailabilityActions
{
    /**
     * Verificar disponibilidad del instructor para una nueva ficha
     */
    public function verificarDisponibilidad(VerificarDisponibilidadRequest $request, Instructor $instructor)
    {
        try {
            $datosFicha = $request->validated();

            $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'disponibilidad' => $disponibilidad,
                ]);
            }

            return response()->json($disponibilidad);
        } catch (Exception $e) {
            Log::error('Error verificando disponibilidad del instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al verificar disponibilidad',
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al verificar disponibilidad del instructor');
        }
    }

    /**
     * Obtener instructores disponibles para una ficha específica
     */
    public function instructoresDisponibles(InstructoresDisponiblesRequest $request)
    {
        try {
            $criterios = $request->validated();
            $instructoresDisponibles = $this->businessRulesService->obtenerInstructoresDisponibles($criterios);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'instructores' => $instructoresDisponibles,
                    'total' => count($instructoresDisponibles),
                ]);
            }

            return view('instructores.disponibles', compact('instructoresDisponibles', 'criterios'));
        } catch (Exception $e) {
            Log::error('Error obteniendo instructores disponibles', [
                'error' => $e->getMessage(),
                'criterios' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener instructores disponibles',
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al obtener instructores disponibles');
        }
    }
}
