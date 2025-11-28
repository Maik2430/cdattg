<?php

declare(strict_types=1);

namespace App\Inventario\Interfaces\Repositories\Aprobacion;

use App\Models\Inventario\Aprobacion;

interface AprobacionRepositoryInterface
{
    public function crear(array $datos): Aprobacion;
}

