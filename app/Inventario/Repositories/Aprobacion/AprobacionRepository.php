<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Aprobacion;

use App\Models\Inventario\Aprobacion;
use App\Inventario\Interfaces\Repositories\Aprobacion\AprobacionRepositoryInterface;

class AprobacionRepository implements AprobacionRepositoryInterface
{
    /**
     * Crea una nueva aprobación
     *
     * @param array $datos
     * @return Aprobacion
     */
    public function crear(array $datos): Aprobacion
    {
        return Aprobacion::create($datos);
    }
}

