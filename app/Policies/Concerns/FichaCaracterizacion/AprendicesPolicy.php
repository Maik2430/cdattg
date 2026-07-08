<?php

namespace App\Policies\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use App\Models\User;

trait AprendicesPolicy
{
    /**
     * Determine whether the user can view apprentices count for a ficha.
     */
    public function getCantidadAprendicesPorFicha(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->view($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can view apprentices for a ficha.
     */
    public function getAprendicesPorFicha(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->view($user, $fichaCaracterizacion);
    }
}
