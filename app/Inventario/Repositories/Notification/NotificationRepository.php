<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Notification;

use App\Models\User;
use App\Inventario\Interfaces\Repositories\Notification\NotificationRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationRepository implements NotificationRepositoryInterface
{
    /**
     * Obtiene notificaciones paginadas de un usuario
     */
    public function obtenerPorUsuarioPaginadas(int $userId, int $perPage): LengthAwarePaginator
    {
        $user = User::findOrFail($userId);
        return $user->notifications()->paginate($perPage);
    }

    /**
     * Obtiene notificaciones no leídas limitadas
     */
    public function obtenerNoLeidasLimitadas(int $userId, int $limit): Collection
    {
        $user = User::findOrFail($userId);
        return $user->unreadNotifications()->take($limit)->get();
    }

    /**
     * Cuenta notificaciones no leídas
     */
    public function contarNoLeidas(int $userId): int
    {
        $user = User::findOrFail($userId);
        return $user->unreadNotifications()->count();
    }

    /**
     * Marca una notificación como leída
     */
    public function marcarComoLeida(int $userId, string $notificationId): bool
    {
        $user = User::findOrFail($userId);
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Marca todas las notificaciones como leídas
     */
    public function marcarTodasComoLeidas(int $userId): int
    {
        $user = User::findOrFail($userId);
        $count = 0;

        $user->unreadNotifications->each(function ($notification) use (&$count): void {
            $notification->markAsRead();
            $count++;
        });

        return $count;
    }

    /**
     * Elimina una notificación
     */
    public function eliminar(int $userId, string $notificationId): bool
    {
        $user = User::findOrFail($userId);
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if ($notification) {
            $notification->delete();
            return true;
        }

        return false;
    }
}

