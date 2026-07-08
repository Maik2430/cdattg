<?php

namespace App\Policies\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use App\Models\User;

trait ReportePolicy
{
    /**
     * Determine whether the user can validate ficha deletion.
     */
    public function validarEliminacionFicha(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->delete($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can generate ficha reports.
     */
    public function generarReporteFicha(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->view($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can generate general reports.
     */
    public function generarReporteGeneral(User $user): bool
    {
        return $user->can('VER FICHA CARACTERIZACION') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can export fichas.
     */
    public function exportarFichas(User $user): bool
    {
        return $user->can('VER FICHA CARACTERIZACION') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }
}
