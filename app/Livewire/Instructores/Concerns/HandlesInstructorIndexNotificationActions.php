<?php

namespace App\Livewire\Instructores\Concerns;

trait HandlesInstructorIndexNotificationActions
{
    public function showNotification($data)
    {
        // Este método maneja las notificaciones desde el backend
        // El JavaScript se encargará de mostrarlas visualmente
        // NO volver a disparar notify para evitar bucle infinito
    }
}
