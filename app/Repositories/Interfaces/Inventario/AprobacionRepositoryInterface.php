<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces\Inventario;

use App\Models\Inventario\Aprobacion;

interface AprobacionRepositoryInterface
{
    public function crear(array $datos): Aprobacion;
}
