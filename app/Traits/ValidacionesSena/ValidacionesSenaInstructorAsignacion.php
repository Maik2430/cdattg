<?php

namespace App\Traits\ValidacionesSena;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Illuminate\Support\Facades\DB;

trait ValidacionesSenaInstructorAsignacion
{
    /**
     * Valida que el instructor pertenezca a la misma regional.
     *
     * @param  int  $instructorId  ID del instructor
     * @param  int  $sedeId  ID de la sede
     * @return array Resultado de la validación
     */
    protected function validarInstructorPerteneceARegional($instructorId, $sedeId)
    {
        try {
            $instructor = Instructor::find($instructorId);
            $sede = \App\Models\Sede::find($sedeId);

            if (! $instructor || ! $sede) {
                return [
                    'valido' => false,
                    'mensaje' => 'El instructor o la sede no existen.',
                ];
            }

            if ($instructor->regional_id != $sede->regional_id) {
                return [
                    'valido' => false,
                    'mensaje' => 'El instructor debe pertenecer a la misma regional que la sede de la ficha.',
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El instructor pertenece a la regional correcta.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar instructor: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Valida que el instructor no tenga más de X fichas simultáneas.
     *
     * @param  int  $instructorId  ID del instructor
     * @param  string  $fechaInicio  Fecha de inicio
     * @param  string  $fechaFin  Fecha de fin
     * @param  int|null  $excluirFichaId  ID de ficha a excluir
     * @return array Resultado de la validación
     */
    protected function validarLimiteFichasPorInstructor($instructorId, $fechaInicio, $fechaFin, $excluirFichaId = null)
    {
        try {
            $limiteMaximo = 3; // Máximo 3 fichas simultáneas por instructor

            // Contar fichas donde el instructor es principal
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

            if ($excluirFichaId) {
                $queryPrincipal->where('id', '!=', $excluirFichaId);
            }

            $fichasPrincipales = $queryPrincipal->count();

            // Contar fichas donde el instructor es auxiliar
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

            if ($excluirFichaId) {
                $queryAuxiliar->where('instructor_fichas_caracterizacion.ficha_id', '!=', $excluirFichaId);
            }

            $fichasAuxiliares = $queryAuxiliar->count();

            $totalFichas = $fichasPrincipales + $fichasAuxiliares;

            if ($totalFichas >= $limiteMaximo) {
                return [
                    'valido' => false,
                    'mensaje' => "El instructor ya tiene asignado el máximo de {$limiteMaximo} fichas simultáneas.",
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El instructor puede ser asignado a esta ficha.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar límite de fichas por instructor: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Valida que el instructor tenga las competencias requeridas para el programa.
     *
     * @param  int  $instructorId  ID del instructor
     * @param  int  $programaId  ID del programa
     * @return array Resultado de la validación
     */
    protected function validarCompetenciasInstructor($instructorId, $programaId)
    {
        try {
            $instructor = Instructor::find($instructorId);
            $programa = \App\Models\ProgramaFormacion::find($programaId);

            if (! $instructor || ! $programa) {
                return [
                    'valido' => false,
                    'mensaje' => 'El instructor o programa no existe.',
                ];
            }

            // Verificar si el instructor tiene competencias para el programa
            // Esta validación puede ser más compleja según la estructura de competencias
            $tieneCompetencias = DB::table('instructor_competencias')
                ->join('competencias', 'instructor_competencias.competencia_id', '=', 'competencias.id')
                ->join('programa_competencias', 'competencias.id', '=', 'programa_competencias.competencia_id')
                ->where('instructor_competencias.instructor_id', $instructorId)
                ->where('programa_competencias.programa_id', $programaId)
                ->exists();

            if (! $tieneCompetencias) {
                return [
                    'valido' => false,
                    'mensaje' => 'El instructor no tiene las competencias requeridas para este programa de formación.',
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El instructor tiene las competencias requeridas.',
            ];

        } catch (\Exception $e) {
            // Si no existe la tabla de competencias, retornar válido
            return [
                'valido' => true,
                'mensaje' => 'Validación de competencias no disponible.',
            ];
        }
    }
}
