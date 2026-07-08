<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait BackupPolicy
{
    /**
     * Determine whether the user can manage instructor backups.
     * Solo administradores pueden gestionar backups.
     */
    public function gestionarBackups(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('GESTIONAR BACKUPS INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden gestionar backups
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can create backup.
     */
    public function crearBackup(User $user, Instructor $instructor): bool
    {
        return $this->gestionarBackups($user, $instructor);
    }

    /**
     * Determine whether the user can restore backup.
     */
    public function restaurarBackup(User $user, Instructor $instructor): bool
    {
        return $this->gestionarBackups($user, $instructor);
    }

    /**
     * Determine whether the user can view backups.
     */
    public function backups(User $user, Instructor $instructor): bool
    {
        return $this->gestionarBackups($user, $instructor);
    }
}
