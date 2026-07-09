<?php

namespace App\Services\Concerns\FichaCaracterizacionValidation;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaCaracterizacionValidationEliminacionActions
{
    /**
     * Valida si una ficha puede ser eliminada según las reglas de negocio.
     *
     * @param  int  $fichaId  ID de la ficha
     * @return array Resultado de la validación
     */
    public function validarEliminacionFicha($fichaId)
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

            if ($ficha->tieneAprendices()) {
                $errores[] = 'No se puede eliminar la ficha porque tiene aprendices asignados.';
            }

            $tieneAsistencias = DB::table('asistencia_aprendices')
                ->join('aprendices', 'asistencia_aprendices.aprendiz_id', '=', 'aprendices.id')
                ->where('aprendices.ficha_caracterizacion_id', $fichaId)
                ->whereNull('aprendices.deleted_at')
                ->exists();

            if ($tieneAsistencias) {
                $errores[] = 'No se puede eliminar la ficha porque tiene asistencias registradas.';
            }

            if ($ficha->fecha_inicio && $ficha->fecha_inicio <= now()) {
                $errores[] = 'No se puede eliminar la ficha porque el programa ya ha comenzado.';
            }

            if ($ficha->instructorFicha()->count() > 0) {
                $errores[] = 'No se puede eliminar la ficha porque tiene instructores asignados.';
            }

            return [
                'valido' => empty($errores),
                'mensaje' => empty($errores)
                    ? 'La ficha puede ser eliminada.'
                    : implode(' ', $errores),
                'errores' => $errores,
            ];
        } catch (\Exception $e) {
            Log::error('Error al validar eliminación de ficha', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'valido' => false,
                'mensaje' => 'Error interno al validar eliminación: '.$e->getMessage(),
                'errores' => ['Error interno en la validación'],
            ];
        }
    }
}
