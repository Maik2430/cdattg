<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesFichaInstructorGestionActions
{
    public function gestionarInstructores(string $id)
    {
        try {
            Log::info('Acceso a gestión de instructores con sistema robusto', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'timestamp' => now(),
            ]);

            $ficha = FichaCaracterizacion::with([
                'instructor.persona',
                'instructorFicha.instructor.persona',
                'instructorFicha.instructorFichaDias.dia',
                'diasFormacion.dia',
                'programaFormacion.redConocimiento',
                'sede.regional',
                'jornadaFormacion.parametro',
            ])->findOrFail($id);

            if (! $ficha->status) {
                return redirect()->route('fichaCaracterizacion.show', $id)
                    ->with('warning', 'La ficha no está activa. No se pueden gestionar instructores.');
            }

            $instructoresAsignados = $ficha->instructorFicha()
                ->with(['competencia', 'resultadosAprendizaje'])
                ->with(['instructor.persona', 'instructorFichaDias.dia'])
                ->get();

            $this->recalcularHorasInstructoresAsignados($instructoresAsignados);

            $diasFormacionFicha = $ficha->diasFormacion()
                ->with('dia')
                ->get()
                ->filter(function ($diaFormacion) {
                    return $diaFormacion->dia !== null;
                })
                ->unique('dia_id')
                ->values()
                ->sortBy('dia_id');

            Log::info('Días de formación cargados para ficha', [
                'ficha_id' => $ficha->id,
                'total_dias' => $diasFormacionFicha->count(),
                'dias' => $diasFormacionFicha->map(function ($df) {
                    return [
                        'dia_id' => $df->dia_id,
                        'dia_nombre' => $df->dia->name ?? 'N/A',
                        'hora_inicio' => $df->hora_inicio,
                        'hora_fin' => $df->hora_fin,
                    ];
                })->toArray(),
            ]);

            $diasSemana = \App\Models\Parametro::whereHas('parametrosTemas', function ($query) {
                $query->where('tema_id', 4);
            })->orderBy('id')->get();

            $asignacionService = app(\App\Services\AsignacionInstructorService::class);
            $instructoresConDisponibilidad = $asignacionService->obtenerInstructoresDisponibles((int) $id);

            $instructorLiderId = $ficha->instructor_id;
            $instructoresAsignadosIds = $instructoresAsignados->pluck('instructor_id')->toArray();

            Log::info('🔍 DEBUG FILTRADO INSTRUCTORES', [
                'instructor_lider_id' => $instructorLiderId,
                'instructores_asignados_ids' => $instructoresAsignadosIds,
                'total_disponibles_antes_filtro' => count($instructoresConDisponibilidad),
            ]);

            $instructoresConDisponibilidad = array_filter($instructoresConDisponibilidad, function ($instructorData) use ($instructoresAsignadosIds, $instructorLiderId) {
                $instructorId = $instructorData['instructor']->id;

                if ($instructorId == $instructorLiderId) {
                    Log::info('🔍 INCLUYENDO INSTRUCTOR LÍDER', ['instructor_id' => $instructorId]);

                    return true;
                }

                $incluir = ! in_array($instructorId, $instructoresAsignadosIds);
                if (! $incluir) {
                    Log::info('🔍 EXCLUYENDO INSTRUCTOR ASIGNADO', ['instructor_id' => $instructorId]);
                }

                return $incluir;
            });

            $instructoresConDisponibilidad = array_values($instructoresConDisponibilidad);
            $estadisticasAsignaciones = $asignacionService->obtenerEstadisticasAsignaciones();
            $logsRecientes = \App\Models\AsignacionInstructorLog::where('ficha_id', $id)
                ->with(['instructor.persona', 'user'])
                ->orderBy('fecha_accion', 'desc')
                ->limit(10)
                ->get();

            Log::info('Datos de gestión de instructores cargados con sistema robusto', [
                'ficha_id' => $id,
                'total_instructores_evaluados' => count($instructoresConDisponibilidad),
                'instructores_disponibles' => count(array_filter($instructoresConDisponibilidad, fn ($i) => $i['disponible'])),
                'instructores_asignados' => $instructoresAsignados->count(),
                'logs_recientes' => $logsRecientes->count(),
            ]);

            return view('fichas.gestionar-instructores', compact(
                'ficha',
                'instructoresAsignados',
                'instructoresConDisponibilidad',
                'diasFormacionFicha',
                'diasSemana',
                'estadisticasAsignaciones',
                'logsRecientes'
            ));
        } catch (\Exception $e) {
            Log::error('Error al cargar gestión de instructores', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'Error al cargar la gestión de instructores: '.$e->getMessage());
        }
    }
}
