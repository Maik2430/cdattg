<?php

namespace App\Traits\ValidacionesSena;

use App\Models\Ambiente;
use App\Models\FichaCaracterizacion;

trait ValidacionesSenaAmbienteDisponibilidad
{
    /**
     * Valida la disponibilidad de un ambiente en un rango de fechas específico.
     *
     * @param  int  $ambienteId  ID del ambiente
     * @param  string  $fechaInicio  Fecha de inicio
     * @param  string  $fechaFin  Fecha de fin
     * @param  int|null  $excluirFichaId  ID de ficha a excluir (para actualizaciones)
     * @return array Resultado de la validación
     */
    protected function validarDisponibilidadAmbiente($ambienteId, $fechaInicio, $fechaFin, $excluirFichaId = null)
    {
        try {
            // Verificar que el ambiente existe y está activo
            $ambiente = Ambiente::find($ambienteId);
            if (! $ambiente) {
                return [
                    'valido' => false,
                    'mensaje' => 'El ambiente seleccionado no existe.',
                ];
            }

            // Verificar que el ambiente esté disponible (sin restricciones de mantenimiento)
            if (isset($ambiente->estado) && $ambiente->estado === 'MANTENIMIENTO') {
                return [
                    'valido' => false,
                    'mensaje' => 'El ambiente está en mantenimiento y no está disponible.',
                ];
            }

            // Buscar fichas que usen el mismo ambiente en el mismo rango de fechas
            $query = FichaCaracterizacion::where('ambiente_id', $ambienteId)
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
                $query->where('id', '!=', $excluirFichaId);
            }

            $fichasConflictivas = $query->get();

            if ($fichasConflictivas->count() > 0) {
                $fichasConflictivasStr = $fichasConflictivas->pluck('ficha')->implode(', ');

                return [
                    'valido' => false,
                    'mensaje' => "El ambiente no está disponible en las fechas seleccionadas. Ya está siendo usado por las fichas: {$fichasConflictivasStr}.",
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El ambiente está disponible.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar disponibilidad del ambiente: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Valida que no haya superposición de horarios en el mismo ambiente.
     *
     * @param  int  $ambienteId  ID del ambiente
     * @param  string  $fechaInicio  Fecha de inicio
     * @param  string  $fechaFin  Fecha de fin
     * @param  int|null  $excluirFichaId  ID de ficha a excluir
     * @return array Resultado de la validación
     */
    protected function validarSuperposicionHorariosAmbiente($ambienteId, $fechaInicio, $fechaFin, $excluirFichaId = null)
    {
        try {
            // Buscar fichas que usen el mismo ambiente con superposición de horarios
            $query = FichaCaracterizacion::where('ambiente_id', $ambienteId)
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
                $query->where('id', '!=', $excluirFichaId);
            }

            $fichasConflictivas = $query->with(['programaFormacion', 'jornadaFormacion'])->get();

            if ($fichasConflictivas->count() > 0) {
                $conflictos = $fichasConflictivas->map(function ($ficha) {
                    return "Ficha {$ficha->ficha} (".($ficha->programaFormacion->nombre ?? 'N/A').') - Jornada: '.($ficha->jornadaFormacion->jornada ?? 'N/A');
                })->implode(', ');

                return [
                    'valido' => false,
                    'mensaje' => "El ambiente tiene conflictos de horario con las siguientes fichas: {$conflictos}.",
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'No hay conflictos de horario en el ambiente.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar superposición de horarios: '.$e->getMessage(),
            ];
        }
    }
}
