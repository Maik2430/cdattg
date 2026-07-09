<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Aprobacion;

use App\Inventario\Interfaces\Repositories\Aprobacion\AprobacionRepositoryInterface;
use App\Models\Inventario\Aprobacion;

class AprobacionRepository implements AprobacionRepositoryInterface
{
    /**
     * Crea una nueva aprobación
     */
    public function crear(array $datos): Aprobacion
    {
        return Aprobacion::create($datos);
    }
}
