<?php

declare(strict_types=1);

namespace App\Inventario\Interfaces\Services;

use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function obtenerSuperAdministradores(): Collection;
}

