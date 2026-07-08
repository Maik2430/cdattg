<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\User;

trait ApiPolicy
{
    /**
     * Determine whether the user can view API guías.
     */
    public function apiIndex(User $user): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can view API guía.
     */
    public function apiShow(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }
}
