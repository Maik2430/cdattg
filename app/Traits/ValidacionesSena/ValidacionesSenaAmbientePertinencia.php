<?php

namespace App\Traits\ValidacionesSena;

use App\Models\Ambiente;

trait ValidacionesSenaAmbientePertinencia
{
    /**
     * Valida que el ambiente pertenezca a la misma sede.
     *
     * @param  int  $ambienteId  ID del ambiente
     * @param  int  $sedeId  ID de la sede
     * @return array Resultado de la validación
     */
    protected function validarAmbientePerteneceASede($ambienteId, $sedeId)
    {
        try {
            $ambiente = Ambiente::with(['piso.bloque'])->find($ambienteId);
            if (! $ambiente) {
                return [
                    'valido' => false,
                    'mensaje' => 'El ambiente seleccionado no existe.',
                ];
            }

            // Obtener la sede del ambiente a través de la relación: Ambiente -> Piso -> Bloque -> Sede
            $sedeAmbiente = $ambiente->piso?->bloque?->sede_id;

            if (! $sedeAmbiente) {
                // Si no hay relación, permitir (puede ser ambiente externo o sin estructura definida)
                return [
                    'valido' => true,
                    'mensaje' => 'El ambiente no tiene sede definida, se permite la asignación.',
                ];
            }

            if ($sedeAmbiente != $sedeId) {
                return [
                    'valido' => false,
                    'mensaje' => 'El ambiente seleccionado no pertenece a la sede de la ficha.',
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El ambiente pertenece a la sede correcta.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar ambiente: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Valida que el ambiente tenga la capacidad adecuada para el programa.
     *
     * @param  int  $ambienteId  ID del ambiente
     * @param  int  $programaId  ID del programa
     * @return array Resultado de la validación
     */
    protected function validarCapacidadAmbiente($ambienteId, $programaId)
    {
        try {
            $ambiente = Ambiente::find($ambienteId);
            $programa = \App\Models\ProgramaFormacion::find($programaId);

            if (! $ambiente || ! $programa) {
                return [
                    'valido' => false,
                    'mensaje' => 'El ambiente o programa no existe.',
                ];
            }

            // Capacidades mínimas requeridas por tipo de programa
            $capacidadesRequeridas = [
                'TÉCNICO' => 25,
                'TECNÓLOGO' => 25,
                'AUXILIAR' => 30,
                'OPERARIO' => 35,
            ];

            $tipoPrograma = $programa->nivel ?? 'TÉCNICO';
            $capacidadRequerida = $capacidadesRequeridas[$tipoPrograma] ?? 25;

            // Verificar si el ambiente tiene campo de capacidad
            if (isset($ambiente->capacidad) && $ambiente->capacidad < $capacidadRequerida) {
                return [
                    'valido' => false,
                    'mensaje' => "El ambiente no tiene la capacidad suficiente para el programa {$tipoPrograma}. Capacidad requerida: {$capacidadRequerida}, capacidad del ambiente: {$ambiente->capacidad}.",
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El ambiente tiene la capacidad adecuada para el programa.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar capacidad del ambiente: '.$e->getMessage(),
            ];
        }
    }
}
