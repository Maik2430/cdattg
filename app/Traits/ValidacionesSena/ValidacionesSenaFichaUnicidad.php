<?php

namespace App\Traits\ValidacionesSena;

use App\Models\FichaCaracterizacion;
use Carbon\Carbon;

trait ValidacionesSenaFichaUnicidad
{
    /**
     * Valida que el número de ficha sea único por programa de formación.
     *
     * @param  string  $numeroFicha  Número de ficha
     * @param  int  $programaId  ID del programa de formación
     * @param  int|null  $excluirFichaId  ID de ficha a excluir (para actualizaciones)
     * @return array Resultado de la validación
     */
    protected function validarFichaUnicaPorPrograma($numeroFicha, $programaId, $excluirFichaId = null)
    {
        try {
            $query = FichaCaracterizacion::where('ficha', $numeroFicha)
                ->where('programa_formacion_id', $programaId);

            // Excluir la ficha actual si se está actualizando
            if ($excluirFichaId) {
                $query->where('id', '!=', $excluirFichaId);
            }

            $fichaExistente = $query->first();

            if ($fichaExistente) {
                return [
                    'valido' => false,
                    'mensaje' => "Ya existe una ficha con el número '{$numeroFicha}' en este programa de formación.",
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El número de ficha es válido para este programa.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar unicidad de la ficha: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Valida los horarios según la jornada seleccionada.
     *
     * @param  int  $jornadaId  ID de la jornada
     * @param  string  $fechaInicio  Fecha de inicio
     * @return array Resultado de la validación
     */
    protected function validarHorariosSegunJornada($jornadaId, $fechaInicio)
    {
        try {
            $fechaInicio = Carbon::parse($fechaInicio);
            $diaSemana = $fechaInicio->dayOfWeek; // 0 = Domingo, 1 = Lunes, etc.

            // Configuración de jornadas y días permitidos
            $configuracionJornadas = [
                1 => ['nombre' => 'MAÑANA', 'dias_permitidos' => [1, 2, 3, 4, 5, 6]], // Lunes a Sábado
                2 => ['nombre' => 'TARDE', 'dias_permitidos' => [1, 2, 3, 4, 5, 6]], // Lunes a Sábado
                3 => ['nombre' => 'NOCHE', 'dias_permitidos' => [1, 2, 3, 4, 5, 6]], // Lunes a Sábado
                4 => ['nombre' => 'FIN DE SEMANA', 'dias_permitidos' => [6]], // Sábado
                5 => ['nombre' => 'MIXTA', 'dias_permitidos' => [1, 2, 3, 4, 5, 6]], // Lunes a Sábado
            ];

            if (isset($configuracionJornadas[$jornadaId])) {
                $diasPermitidos = $configuracionJornadas[$jornadaId]['dias_permitidos'];
                if (! in_array($diaSemana, $diasPermitidos)) {
                    return [
                        'valido' => false,
                        'mensaje' => "La fecha de inicio no es compatible con la jornada {$configuracionJornadas[$jornadaId]['nombre']}.",
                    ];
                }
            }

            return [
                'valido' => true,
                'mensaje' => 'Los horarios son válidos para la jornada seleccionada.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar horarios: '.$e->getMessage(),
            ];
        }
    }
}
