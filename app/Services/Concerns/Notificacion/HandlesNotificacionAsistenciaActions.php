<?php

namespace App\Services\Concerns\Notificacion;

use Illuminate\Support\Facades\Log;

trait HandlesNotificacionAsistenciaActions
{
    public function notificarAsistenciaRegistrada(array $datosAsistencia): bool
    {
        try {
            broadcast(new \App\Events\NuevaAsistenciaRegistrada($datosAsistencia))->toOthers();

            Log::info('Notificación de asistencia broadcast', [
                'aprendiz' => $datosAsistencia['aprendiz'] ?? 'N/A',
                'estado' => $datosAsistencia['estado'] ?? 'N/A',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error en broadcast de asistencia', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
