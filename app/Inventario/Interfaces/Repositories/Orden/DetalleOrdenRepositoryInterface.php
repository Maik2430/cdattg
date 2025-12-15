<?php

declare(strict_types=1);

namespace App\Inventario\Interfaces\Repositories\Orden;

use App\Models\Inventario\DetalleOrden;

interface DetalleOrdenRepositoryInterface
{
    public function crear(array $datos): DetalleOrden;
    public function actualizar(DetalleOrden $detalleOrden, array $datos): bool;
    public function eliminar(DetalleOrden $detalleOrden): bool;
    public function eliminarPorOrden(int $ordenId): bool;
    public function encontrar(int $id): ?DetalleOrden;
    public function encontrarConRelaciones(int $id): ?DetalleOrden;
}

