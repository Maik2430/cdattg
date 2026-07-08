<?php

namespace App\Traits\ValidacionesSena;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Illuminate\Support\Facades\DB;

trait ValidacionesSenaInstructorDisponibilidad
{
    /**
     * Valida la disponibilidad de un instructor en un rango de fechas específico.
     *
     * @param  int  $instructorId  ID del instructor
     * @param  string  $fechaInicio  Fecha de inicio
     * @param  string  $fechaFin  Fecha de fin
     * @param  int|null  $excluirFichaId  ID de ficha a excluir (para actualizaciones)
     * @return array Resultado de la validación
     */
    protected function validarDisponibilidadInstructor($instructorId, $fechaInicio, $fechaFin, $excluirFichaId = null)
    {
        try {
            // Verificar que el instructor existe y está activo
            $instructor = Instructor::with('persona')->find($instructorId);
            if (! $instructor) {
                return [
                    'valido' => false,
                    'mensaje' => 'El instructor seleccionado no existe.',
                ];
            }

            // Verificar que el instructor esté activo (si tiene campo status)
            if (isset($instructor->status) && ! $instructor->status) {
                return [
                    'valido' => false,
                    'mensaje' => 'El instructor no está activo en el sistema.',
                ];
            }

            // Buscar fichas donde el instructor sea instructor principal
            $queryPrincipal = FichaCaracterizacion::where('instructor_id', $instructorId)
                ->where('status', true)
                ->where(function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                        ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                        ->orWhere(function ($subQuery) use ($fechaInicio, $fechaFin) {
                            $subQuery->where('fecha_inicio', '<=', $fechaInicio)
                                ->where('fecha_fin', '>=', $fechaFin);
                        });
                });

            // Excluir la ficha actual si se está actualizando
            if ($excluirFichaId) {
                $queryPrincipal->where('id', '!=', $excluirFichaId);
            }

            $fichasPrincipales = $queryPrincipal->get();

            // Buscar fichas donde el instructor esté asignado como auxiliar
            $queryAuxiliar = DB::table('instructor_fichas_caracterizacion')
                ->join('fichas_caracterizacion', 'instructor_fichas_caracterizacion.ficha_id', '=', 'fichas_caracterizacion.id')
                ->where('instructor_fichas_caracterizacion.instructor_id', $instructorId)
                ->where('fichas_caracterizacion.status', true)
                ->where(function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('instructor_fichas_caracterizacion.fecha_inicio', [$fechaInicio, $fechaFin])
                        ->orWhereBetween('instructor_fichas_caracterizacion.fecha_fin', [$fechaInicio, $fechaFin])
                        ->orWhere(function ($subQuery) use ($fechaInicio, $fechaFin) {
                            $subQuery->where('instructor_fichas_caracterizacion.fecha_inicio', '<=', $fechaInicio)
                                ->where('instructor_fichas_caracterizacion.fecha_fin', '>=', $fechaFin);
                        });
                });

            // Excluir la ficha actual si se está actualizando
            if ($excluirFichaId) {
                $queryAuxiliar->where('instructor_fichas_caracterizacion.ficha_id', '!=', $excluirFichaId);
            }

            $fichasAuxiliares = $queryAuxiliar->get();

            $totalConflictos = $fichasPrincipales->count() + $fichasAuxiliares->count();

            if ($totalConflictos > 0) {
                $fichasConflictivas = collect()
                    ->merge($fichasPrincipales->pluck('ficha'))
                    ->merge($fichasAuxiliares->pluck('ficha'))
                    ->unique()
                    ->implode(', ');

                return [
                    'valido' => false,
                    'mensaje' => "El instructor {$instructor->persona->primer_nombre} {$instructor->persona->primer_apellido} no está disponible en las fechas seleccionadas. Ya está asignado a las fichas: {$fichasConflictivas}.",
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El instructor está disponible.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar disponibilidad del instructor: '.$e->getMessage(),
            ];
        }
    }
}
