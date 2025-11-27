<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Inventario;

use App\Models\Inventario\Aprobacion;
use App\Repositories\Interfaces\Inventario\AprobacionRepositoryInterface;

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
