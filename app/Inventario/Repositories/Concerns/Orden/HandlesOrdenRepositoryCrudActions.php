<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Concerns\Orden;

use App\Models\Inventario\Orden;

trait HandlesOrdenRepositoryCrudActions
{
    public function crear(array $datos): Orden
    {
        return Orden::create($datos);
    }

    public function actualizar(Orden $orden, array $datos): bool
    {
        return $orden->update($datos);
    }

    public function eliminar(Orden $orden): bool
    {
        return $orden->delete();
    }
}
