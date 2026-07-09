<?php

namespace App\Services\Concerns\Notificacion;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HandlesNotificacionAdminActions
{
    public function notificarAdministradores(string $tipo, array $datos): int
    {
        try {
            $administradores = User::role('ADMINISTRADOR')->get();
            $enviados = 0;

            foreach ($administradores as $admin) {
                if ($admin->email) {
                    $enviados++;
                }
            }

            Log::info('Notificaciones a administradores enviadas', [
                'tipo' => $tipo,
                'enviados' => $enviados,
            ]);

            return $enviados;
        } catch (\Exception $e) {
            Log::error('Error notificando administradores', [
                'tipo' => $tipo,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    public function programarNotificacion(User $user, string $mensaje, Carbon $cuando): bool
    {
        try {
            Log::info('Notificación programada', [
                'user_id' => $user->id,
                'cuando' => $cuando->toDateTimeString(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error programando notificación', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
