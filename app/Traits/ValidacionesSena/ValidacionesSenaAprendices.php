<?php

namespace App\Traits\ValidacionesSena;

use Illuminate\Support\Facades\DB;

trait ValidacionesSenaAprendices
{
    /**
     * Valida el límite de aprendices por programa.
     *
     * @param  int  $programaId  ID del programa
     * @return array Resultado de la validación
     */
    /**
     * Valida el límite de aprendices por ficha de caracterización.
     * El límite se determina según el tipo de programa de formación.
     *
     * @param  int  $fichaId  ID de la ficha de caracterización
     * @param  int  $programaId  ID del programa de formación (para obtener el tipo y límite)
     * @return array Resultado de la validación
     */
    protected function validarLimiteAprendicesPorFicha($fichaId, $programaId)
    {
        try {
            $programa = \App\Models\ProgramaFormacion::find($programaId);
            if (! $programa) {
                return [
                    'valido' => false,
                    'mensaje' => 'El programa de formación no existe.',
                ];
            }

            // Límites según el tipo de programa (estos valores pueden configurarse)
            $limitesPorTipo = [
                'TÉCNICO' => 30,
                'TECNÓLOGO' => 25,
                'AUXILIAR' => 35,
                'OPERARIO' => 40,
            ];

            $tipoPrograma = $programa->nivel ?? 'TÉCNICO';
            $limiteMaximo = $limitesPorTipo[$tipoPrograma] ?? 30;

            // Contar aprendices de la ficha específica
            $aprendicesEnFicha = DB::table('aprendices')
                ->where('ficha_caracterizacion_id', $fichaId)
                ->whereNull('deleted_at')
                ->count();

            if ($aprendicesEnFicha >= $limiteMaximo) {
                return [
                    'valido' => false,
                    'mensaje' => "Se ha alcanzado el límite máximo de {$limiteMaximo} aprendices para esta ficha de caracterización.",
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El límite de aprendices está dentro del rango permitido.',
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar límite de aprendices: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @deprecated Esta función está obsoleta. Usar validarLimiteAprendicesPorFicha en su lugar.
     * Mantenida por compatibilidad temporal.
     */
    private function validarLimiteAprendicesPorPrograma($programaId, $excluirFichaId = null)
    {
        // Si se proporciona un fichaId, usar la nueva función
        if ($excluirFichaId !== null) {
            return $this->validarLimiteAprendicesPorFicha($excluirFichaId, $programaId);
        }

        // Si no hay fichaId, no validar (al crear una ficha nueva aún no tiene aprendices)
        return [
            'valido' => true,
            'mensaje' => 'Validación omitida: ficha nueva sin aprendices.',
        ];
    }
}
