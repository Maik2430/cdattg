<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Orden;

use App\Exceptions\OrdenException;
use App\Models\Inventario\Orden;

trait HandlesOrdenDetalleHelpers
{
    /**
     * Procesa un detalle de orden
     *
     * @throws OrdenException
     */
    private function procesarDetalleOrden(Orden $orden, array $productoData, int $userId): void
    {
        $producto = $this->productoRepository->encontrar($productoData['producto_id']);

        if (! $producto) {
            throw new OrdenException("Producto con ID {$productoData['producto_id']} no encontrado.");
        }

        $this->stockValidator->validarStockSuficiente($producto, $productoData['cantidad']);

        $this->detalleOrdenRepository->crear([
            'orden_id' => $orden->id,
            'producto_id' => $producto->id,
            'cantidad' => $productoData['cantidad'],
            'estado_orden_id' => $productoData['estado_orden_id'],
            'user_create_id' => $userId,
            'user_update_id' => $userId,
        ]);

        $cantidadAnterior = $producto->cantidad;
        $nuevaCantidad = $cantidadAnterior - $productoData['cantidad'];
        $this->productoRepository->actualizarStock($producto, $nuevaCantidad);
        $this->stockValidator->verificarYNotificarCambioStock($producto, $cantidadAnterior);
    }
}
