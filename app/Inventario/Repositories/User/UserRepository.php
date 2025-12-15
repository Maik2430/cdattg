<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\User;

use App\Inventario\Interfaces\Services\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function obtenerSuperAdministradores(): Collection
    {
        return User::role('SUPER ADMINISTRADOR')->get();
    }
}

