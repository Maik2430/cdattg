<?php

namespace App\Livewire\Fichas\Concerns;

use Livewire\Attributes\On;

trait HandlesFichaIndexNotificationActions
{
    #[On('showNotification')]
    public function showNotification($data)
    {
        // Este método es para el sistema de notificaciones
        // El JavaScript manejará la visualización
        // El evento puede venir como array o como parámetros separados
        if (is_array($data)) {
            $type = $data['type'] ?? 'info';
            $message = $data['message'] ?? 'Notificación';
        } else {
            // Si vienen como parámetros separados (compatibilidad)
            $args = func_get_args();
            $type = $args[0] ?? 'info';
            $message = $args[1] ?? 'Notificación';
        }
    }
}
