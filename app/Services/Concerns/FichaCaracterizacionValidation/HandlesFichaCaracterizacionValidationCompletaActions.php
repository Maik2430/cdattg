<?php

namespace App\Services\Concerns\FichaCaracterizacionValidation;

use Illuminate\Support\Facades\Log;

trait HandlesFichaCaracterizacionValidationCompletaActions
{
    /**
     * Valida completamente una ficha de caracterización antes de guardarla.
     *
     * @param  array  $datos  Datos de la ficha
     * @param  int|null  $excluirFichaId  ID de ficha a excluir (para actualizaciones)
     * @return array Resultado de la validación
     */
    public function validarFichaCompleta($datos, $excluirFichaId = null)
    {
        $errores = [];
        $advertencias = [];

        try {
            Log::info('Iniciando validación completa de ficha', [
                'datos' => $datos,
                'excluir_ficha_id' => $excluirFichaId,
                'timestamp' => now(),
            ]);

            if (isset($datos['ambiente_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $validacionAmbiente = $this->validarDisponibilidadAmbiente(
                    $datos['ambiente_id'],
                    $datos['fecha_inicio'],
                    $datos['fecha_fin'],
                    $excluirFichaId
                );

                if (! $validacionAmbiente['valido']) {
                    $errores[] = $validacionAmbiente['mensaje'];
                }
            }

            if (isset($datos['instructor_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $validacionInstructor = $this->validarDisponibilidadInstructor(
                    $datos['instructor_id'],
                    $datos['fecha_inicio'],
                    $datos['fecha_fin'],
                    $excluirFichaId
                );

                if (! $validacionInstructor['valido']) {
                    $errores[] = $validacionInstructor['mensaje'];
                }
            }

            if (isset($datos['ficha']) && isset($datos['programa_formacion_id'])) {
                $validacionFicha = $this->validarFichaUnicaPorPrograma(
                    $datos['ficha'],
                    $datos['programa_formacion_id'],
                    $excluirFichaId
                );

                if (! $validacionFicha['valido']) {
                    $errores[] = $validacionFicha['mensaje'];
                }
            }

            $validacionReglas = $this->validarReglasNegocioSena($datos, $excluirFichaId);
            if (! $validacionReglas['valido']) {
                $errores[] = $validacionReglas['mensaje'];
            }

            $validacionesAdicionales = $this->validacionesAdicionales($datos, $excluirFichaId);
            $errores = array_merge($errores, $validacionesAdicionales['errores']);
            $advertencias = array_merge($advertencias, $validacionesAdicionales['advertencias']);

            $resultado = [
                'valido' => empty($errores),
                'errores' => $errores,
                'advertencias' => $advertencias,
                'mensaje' => empty($errores)
                    ? 'Todas las validaciones pasaron correctamente.'
                    : 'Se encontraron errores en la validación.',
            ];

            Log::info('Validación completa de ficha finalizada', [
                'resultado' => $resultado,
                'timestamp' => now(),
            ]);

            return $resultado;
        } catch (\Exception $e) {
            Log::error('Error en validación completa de ficha', [
                'error' => $e->getMessage(),
                'datos' => $datos,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'valido' => false,
                'errores' => ['Error interno en la validación: '.$e->getMessage()],
                'advertencias' => [],
                'mensaje' => 'Error interno en la validación.',
            ];
        }
    }
}
