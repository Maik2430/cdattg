<?php

namespace App\Services\Concerns\FichaCaracterizacionValidation;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaCaracterizacionValidationEdicionActions
{
    /**
     * Valida si una ficha puede ser editada según las reglas de negocio.
     *
     * @param  int  $fichaId  ID de la ficha
     * @return array Resultado de la validación
     */
    public function validarEdicionFicha($fichaId)
    {
        try {
            $ficha = FichaCaracterizacion::find($fichaId);

            if (! $ficha) {
                return [
                    'valido' => false,
                    'mensaje' => 'La ficha no existe.',
                ];
            }

            $errores = [];
            $advertencias = [];

            if ($ficha->tieneAprendices()) {
                $advertencias[] = 'La ficha tiene aprendices asignados. Algunos campos pueden tener restricciones de edición.';
            }

            if ($ficha->fecha_inicio && $ficha->fecha_inicio <= now()) {
                $advertencias[] = 'El programa ya ha comenzado. Las fechas no pueden ser modificadas.';
            }

            $tieneAsistencias = DB::table('asistencia_aprendices')
                ->join('aprendices', 'asistencia_aprendices.aprendiz_id', '=', 'aprendices.id')
                ->where('aprendices.ficha_caracterizacion_id', $fichaId)
                ->whereNull('aprendices.deleted_at')
                ->exists();

            if ($tieneAsistencias) {
                $advertencias[] = 'La ficha tiene asistencias registradas. Algunos cambios pueden afectar los registros existentes.';
            }

            return [
                'valido' => true,
                'mensaje' => 'La ficha puede ser editada.',
                'errores' => $errores,
                'advertencias' => $advertencias,
            ];
        } catch (\Exception $e) {
            Log::error('Error al validar edición de ficha', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'valido' => false,
                'mensaje' => 'Error interno al validar edición: '.$e->getMessage(),
                'errores' => ['Error interno en la validación'],
                'advertencias' => [],
            ];
        }
    }
}
