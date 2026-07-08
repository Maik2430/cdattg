<?php

namespace App\Policies\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use App\Models\User;

trait DiasFormacionPolicy
{
    /**
     * Determine whether the user can manage formation days for a ficha.
     * Los instructores pueden gestionar días de fichas asignadas.
     */
    public function gestionarDiasFormacion(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso específico
        if (! $user->can('GESTIONAR DIAS FICHA')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, verificar que la ficha esté asignada a él
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->fichaAsignadaAInstructor($user, $fichaCaracterizacion);
        }

        return true;
    }

    /**
     * Determine whether the user can save formation days for a ficha.
     */
    public function guardarDiasFormacion(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->gestionarDiasFormacion($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can update a specific formation day.
     */
    public function actualizarDiaFormacion(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->gestionarDiasFormacion($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can delete a specific formation day.
     */
    public function eliminarDiaFormacion(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->gestionarDiasFormacion($user, $fichaCaracterizacion);
    }
}
