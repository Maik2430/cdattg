<?php

namespace App\Services\Concerns\FichaCaracterizacionValidation;

use Illuminate\Support\Facades\Log;

trait HandlesFichaCaracterizacionValidationAdicionalesHelpers
{
    use HandlesFichaCaracterizacionValidationAdicionalesEstadoHelpers;
    use HandlesFichaCaracterizacionValidationAdicionalesPeriodoHelpers;

    /**
     * Validaciones adicionales específicas para el SENA.
     *
     * @param  array  $datos  Datos de la ficha
     * @param  int|null  $excluirFichaId  ID de ficha a excluir
     * @return array Resultado con errores y advertencias
     */
    private function validacionesAdicionales($datos, $excluirFichaId = null)
    {
        try {
            $validacionEstado = $this->validarEntidadesActivasAdicionales($datos);
            $validacionPeriodo = $this->validarPeriodoYDisponibilidadAdicionales($datos, $excluirFichaId);

            return [
                'errores' => array_merge($validacionEstado['errores'], $validacionPeriodo['errores']),
                'advertencias' => $validacionPeriodo['advertencias'],
            ];
        } catch (\Exception $e) {
            Log::error('Error en validaciones adicionales', [
                'error' => $e->getMessage(),
                'datos' => $datos,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'errores' => ['Error en validaciones adicionales: '.$e->getMessage()],
                'advertencias' => [],
            ];
        }
    }
}
