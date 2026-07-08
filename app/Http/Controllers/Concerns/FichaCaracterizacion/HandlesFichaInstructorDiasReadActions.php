<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\Log;

trait HandlesFichaInstructorDiasReadActions
{
    /**
     * Muestra el formulario para gestionar días de formación de un instructor en una ficha.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function gestionarDiasInstructor(string $fichaId, string $instructorFichaId)
    {
        try {
            $ficha = FichaCaracterizacion::with(['programaFormacion', 'sede'])->findOrFail($fichaId);
            $instructorFicha = \App\Models\InstructorFichaCaracterizacion::with(['instructor.persona', 'ficha'])
                ->findOrFail($instructorFichaId);

            // Verificar que el instructor pertenezca a la ficha
            if ($instructorFicha->ficha_id != $fichaId) {
                return redirect()->back()->with('error', 'El instructor no pertenece a esta ficha');
            }

            // Obtener días de la semana (parámetros)
            $diasSemana = \App\Models\Parametro::whereHas('parametrosTemas', function ($query) {
                $query->where('tema_id', 4); // ID del tema "Días de la semana"
            })->orderBy('id')->get();

            // Obtener días ya asignados usando el servicio
            $diasService = app(\App\Services\InstructorFichaDiasService::class);
            $diasAsignados = $diasService->obtenerDiasAsignados($instructorFichaId);

            return view('fichas.instructor-dias', compact(
                'ficha',
                'instructorFicha',
                'diasSemana',
                'diasAsignados'
            ));

        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de días de instructor', [
                'ficha_id' => $fichaId,
                'instructor_ficha_id' => $instructorFichaId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Error al cargar el formulario');
        }
    }

    /**
     * Obtiene los días asignados a un instructor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerDiasInstructor(string $fichaId, string $instructorFichaId)
    {
        try {
            $diasService = app(\App\Services\InstructorFichaDiasService::class);
            $diasAsignados = $diasService->obtenerDiasAsignados($instructorFichaId);

            return response()->json([
                'success' => true,
                'data' => $diasAsignados,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los días asignados',
            ], 500);
        }
    }
}
