<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\User;

trait DuplicacionPolicy
{
    /**
     * Determine whether the user can duplicate guía.
     */
    public function duplicar(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->create($user);
    }

    /**
     * Determine whether the user can create guía from template.
     */
    public function crearDesdePlantilla(User $user): bool
    {
        return $this->create($user);
    }

    /**
     * Determine whether the user can store guía from template.
     */
    public function storeDesdePlantilla(User $user): bool
    {
        return $this->create($user);
    }
}
