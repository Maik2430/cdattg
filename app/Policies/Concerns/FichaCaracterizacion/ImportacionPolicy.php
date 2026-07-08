<?php

namespace App\Policies\Concerns\FichaCaracterizacion;

use App\Models\User;

trait ImportacionPolicy
{
    /**
     * Determine whether the user can download import template.
     */
    public function descargarPlantillaImportacion(User $user): bool
    {
        return $user->can('CREAR FICHA CARACTERIZACION') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can import fichas.
     */
    public function importarFichas(User $user): bool
    {
        return $user->can('CREAR FICHA CARACTERIZACION') &&
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }
}
