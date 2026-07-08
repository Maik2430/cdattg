<?php

namespace App\Models\Concerns\User;

use App\Models\Inventario\Notificacion;

trait HasUserNotifications
{
    /**
     * Especificar la tabla de notificaciones personalizada
     */
    public function notifications()
    {
        return $this->morphMany(Notificacion::class, 'notificable', 'notificable_type', 'notificable_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Obtener las notificaciones leídas de la entidad.
     */
    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('leida_en');
    }

    /**
     * Obtener las notificaciones no leídas de la entidad.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('leida_en');
    }
}
