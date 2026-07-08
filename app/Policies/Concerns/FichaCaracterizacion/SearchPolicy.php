<?php

namespace App\Policies\Concerns\FichaCaracterizacion;

use App\Models\User;

trait SearchPolicy
{
    /**
     * Determine whether the user can search fichas.
     */
    public function search(User $user): bool
    {
        return $user->can('VER FICHA CARACTERIZACION');
    }
}
