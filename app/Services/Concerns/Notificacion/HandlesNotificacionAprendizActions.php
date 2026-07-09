<?php

namespace App\Services\Concerns\Notificacion;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait HandlesNotificacionAprendizActions
{
    public function notificarAprendices(Collection $aprendices, string $mensaje): int
    {
        $enviados = 0;

        foreach ($aprendices as $aprendiz) {
            try {
                $persona = $aprendiz->persona;

                if (! $persona || ! $persona->email) {
                    continue;
                }

                $enviados++;

                Log::info('Notificación enviada a aprendiz', [
                    'aprendiz_id' => $aprendiz->id,
                    'email' => $persona->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Error enviando notificación a aprendiz', [
                    'aprendiz_id' => $aprendiz->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Notificaciones masivas enviadas', [
            'total_aprendices' => $aprendices->count(),
            'enviados' => $enviados,
        ]);

        return $enviados;
    }
}
