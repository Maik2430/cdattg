<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\User;

trait AnaliticasPolicy
{
    /**
     * Determine whether the user can view guía analytics.
     */
    public function analiticas(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can view guía performance.
     */
    public function rendimiento(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }
}
