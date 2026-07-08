<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaAsistenciaHelpers
{
    /**
     * Verifica si una ficha tiene asistencias registradas.
     *
     * @param  int  $fichaId  El ID de la ficha.
     * @return bool True si tiene asistencias, false en caso contrario.
     */
    private function fichaTieneAsistencias(int $fichaId): bool
    {
        try {
            // Buscar asistencias relacionadas con aprendices de esta ficha
            $tieneAsistencias = DB::table('asistencia_aprendices')
                ->join('aprendices', 'asistencia_aprendices.aprendiz_id', '=', 'aprendices.id')
                ->where('aprendices.ficha_caracterizacion_id', $fichaId)
                ->whereNull('aprendices.deleted_at')
                ->exists();

            return $tieneAsistencias;

        } catch (\Exception $e) {
            Log::error('Error al verificar asistencias de ficha', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
            ]);

            // En caso de error, asumir que sí tiene asistencias para ser conservador
            return true;
        }
    }
}
