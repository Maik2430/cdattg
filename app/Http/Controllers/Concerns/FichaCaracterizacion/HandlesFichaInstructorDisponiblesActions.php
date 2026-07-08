<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesFichaInstructorDisponiblesActions
{
    /**
     * Obtiene instructores disponibles para una ficha específica (endpoint AJAX)
     */
    public function obtenerInstructoresDisponiblesParaFicha(string $id, Request $request)
    {
        try {
            Log::info('Solicitud AJAX para obtener instructores disponibles', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'timestamp' => now(),
            ]);

            // Buscar la ficha
            $ficha = FichaCaracterizacion::with([
                'instructor.persona',
                'instructorFicha.instructor.persona',
                'programaFormacion.redConocimiento',
                'sede.regional',
            ])->findOrFail($id);

            // Usar el servicio para obtener instructores disponibles
            $asignacionService = app(\App\Services\AsignacionInstructorService::class);
            $instructoresConDisponibilidad = $asignacionService->obtenerInstructoresDisponibles((int) $id);

            // Obtener la red de conocimiento de la ficha
            $redConocimientoId = $ficha->programaFormacion->red_conocimiento_id ?? null;

            // Filtrar instructores ya asignados
            $instructorLiderId = $ficha->instructor_id;
            $instructoresAsignadosIds = $ficha->instructorFicha()->pluck('instructor_id')->toArray();

            // Filtrar instructores disponibles:
            // 1. Excluir ya asignados (excepto instructor líder)
            // 2. Filtrar por especialidad/red de conocimiento (excepto instructor líder)
            $instructoresDisponibles = collect($instructoresConDisponibilidad)->filter(function ($instructorData) use ($instructorLiderId, $instructoresAsignadosIds, $redConocimientoId) {
                $instructor = $instructorData['instructor'];
                $instructorId = $instructor->id;

                // Incluir instructor líder siempre
                if ($instructorId == $instructorLiderId) {
                    return true;
                }

                // Excluir si ya está asignado
                if (in_array($instructorId, $instructoresAsignadosIds)) {
                    return false;
                }

                // Si hay red de conocimiento definida, filtrar por especialidad
                if ($redConocimientoId !== null) {
                    $especialidades = $instructor->especialidades ?? [];
                    $especialidadPrincipal = $especialidades['principal'] ?? null;
                    $especialidadesSecundarias = $especialidades['secundarias'] ?? [];

                    // Verificar si el instructor tiene la especialidad/red de conocimiento requerida
                    // (puede estar en principal o en secundarias)
                    $tieneEspecialidad = ($especialidadPrincipal == $redConocimientoId) ||
                                       in_array($redConocimientoId, $especialidadesSecundarias);

                    if (! $tieneEspecialidad) {
                        return false;
                    }
                }

                return true;
            })->values();

            // Transformar datos para el frontend (extraer solo lo necesario)
            $instructoresParaFrontend = $instructoresDisponibles->map(function ($instructorData) {
                return [
                    'id' => $instructorData['instructor']->id,
                    'persona' => [
                        'primer_nombre' => $instructorData['instructor']->persona->primer_nombre ?? '',
                        'primer_apellido' => $instructorData['instructor']->persona->primer_apellido ?? '',
                        'numero_documento' => $instructorData['instructor']->persona->numero_documento ?? '',
                    ],
                    'disponible' => $instructorData['disponible'] ?? false,
                    'habilitado' => $instructorData['habilitado'] ?? false, // Puede ser seleccionado solo si pasa todas las validaciones
                    'mensaje_no_disponible' => $instructorData['mensaje_no_disponible'] ?? null, // Mensaje breve para mostrar
                    'razones_no_disponible' => $instructorData['razones_no_disponible'] ?? [],
                    'advertencias' => $instructorData['advertencias'] ?? [],
                ];
            });

            Log::info('Instructores disponibles encontrados', [
                'ficha_id' => $id,
                'total_disponibles' => $instructoresParaFrontend->count(),
                'instructor_lider_id' => $instructorLiderId,
                'instructores_asignados' => $instructoresAsignadosIds,
                'red_conocimiento_id' => $redConocimientoId,
                'red_conocimiento_nombre' => $ficha->programaFormacion->redConocimiento->nombre ?? 'N/A',
                'total_antes_filtro_especialidad' => count($instructoresConDisponibilidad),
            ]);

            return response()->json([
                'success' => true,
                'instructores' => $instructoresParaFrontend,
                'total' => $instructoresParaFrontend->count(),
                'ficha' => [
                    'id' => $ficha->id,
                    'numero' => $ficha->ficha,
                    'programa' => $ficha->programaFormacion->nombre ?? 'N/A',
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Ficha no encontrada para obtener instructores', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'La ficha especificada no existe',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error al obtener instructores disponibles', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener instructores disponibles',
            ], 500);
        }
    }
}
