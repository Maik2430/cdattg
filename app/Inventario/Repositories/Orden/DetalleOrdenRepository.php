<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Orden;

use App\Inventario\Interfaces\Repositories\Orden\DetalleOrdenRepositoryInterface;
use App\Models\Inventario\DetalleOrden;

class DetalleOrdenRepository implements DetalleOrdenRepositoryInterface
{
    /**
     * Crea un nuevo detalle de orden
     */
    public function crear(array $datos): DetalleOrden
    {
        return DetalleOrden::create($datos);
    }

    /**
     * Actualiza un detalle de orden
     */
    public function actualizar(DetalleOrden $detalleOrden, array $datos): bool
    {
        return $detalleOrden->update($datos);
    }

    /**
     * Elimina un detalle de orden
     */
    public function eliminar(DetalleOrden $detalleOrden): bool
    {
        return $detalleOrden->delete();
    }

    /**
     * Elimina todos los detalles de una orden
     */
    public function eliminarPorOrden(int $ordenId): bool
    {
        return DetalleOrden::where('orden_id', $ordenId)->delete() > 0;
    }

    /**
     * Encuentra un detalle de orden por ID
     */
    public function encontrar(int $id): ?DetalleOrden
    {
        return DetalleOrden::find($id);
    }

    /**
     * Encuentra un detalle de orden con relaciones
     */
    public function encontrarConRelaciones(int $id): ?DetalleOrden
    {
        return DetalleOrden::with([
            'orden.tipoOrden.parametro',
            'producto',
            'estadoOrden.parametro',
            'devoluciones',
        ])->find($id);
    }
}
