<?php

namespace App\Policies\Concerns\FichaCaracterizacion;

use App\Models\User;

trait EstadisticasPolicy
{
    /**
     * Determine whether the user can view ficha statistics.
     */
    public function getEstadisticasFichas(User $user): bool
    {
        return $user->can('VER FICHA CARACTERIZACION');
    }

    /**
     * Determine whether the user can view fichas by jornada.
     */
    public function getFichasCaracterizacionPorJornada(User $user): bool
    {
        return $user->can('VER FICHA CARACTERIZACION');
    }

    /**
     * Determine whether the user can view fichas by programa.
     */
    public function getFichasCaracterizacionPorPrograma(User $user): bool
    {
        return $user->can('VER FICHA CARACTERIZACION');
    }

    /**
     * Determine whether the user can view fichas by sede.
     */
    public function getFichasCaracterizacionPorSede(User $user): bool
    {
        return $user->can('VER FICHA CARACTERIZACION');
    }

    /**
     * Determine whether the user can view fichas by instructor.
     */
    public function getFichasCaracterizacionPorInstructor(User $user): bool
    {
        return $user->can('VER FICHA CARACTERIZACION');
    }
}
