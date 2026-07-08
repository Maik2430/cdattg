<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait NotificacionPolicy
{
    /**
     * Determine whether the user can manage instructor notifications.
     * Los instructores pueden gestionar sus propias notificaciones.
     */
    public function gestionarNotificaciones(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('GESTIONAR NOTIFICACIONES INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propias notificaciones
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can mark notification as read.
     */
    public function marcarNotificacionLeida(User $user, Instructor $instructor): bool
    {
        return $this->gestionarNotificaciones($user, $instructor);
    }

    /**
     * Determine whether the user can mark all notifications as read.
     */
    public function marcarTodasLeidas(User $user, Instructor $instructor): bool
    {
        return $this->gestionarNotificaciones($user, $instructor);
    }
}
