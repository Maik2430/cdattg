<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Orden;

use App\Exceptions\OrdenException;
use App\Models\Inventario\Orden;
use Exception;

trait HandlesOrdenActualizacionActions
{
    /**
     * Actualiza una orden existente
     *
     * @throws OrdenException
     */
    public function actualizar(Orden $orden, array $datos, int $userId): Orden
    {
        try {
            $this->transactionService->beginTransaction();

            foreach ($orden->detalles as $detalle) {
                $producto = $detalle->producto;
                $nuevaCantidad = $producto->cantidad + $detalle->cantidad;
                $this->productoRepository->actualizarStock($producto, $nuevaCantidad);
            }

            $this->detalleOrdenRepository->eliminarPorOrden($orden->id);

            $this->ordenRepository->actualizar($orden, [
                'descripcion_orden' => $datos['descripcion_orden'],
                'tipo_orden_id' => $datos['tipo_orden_id'],
                'fecha_devolucion' => $datos['fecha_devolucion'] ?? null,
                'user_update_id' => $userId,
            ]);
            $orden->refresh();

            foreach ($datos['productos'] as $productoData) {
                $this->procesarDetalleOrden($orden, $productoData, $userId);
            }

            $this->transactionService->commit();

            return $orden;

        } catch (Exception $e) {
            $this->transactionService->rollBack();
            throw new OrdenException('Error al actualizar la orden: '.$e->getMessage());
        }
    }

    /**
     * Elimina una orden y devuelve el stock
     *
     * @throws OrdenException
     */
    public function eliminar(Orden $orden): bool
    {
        try {
            $this->transactionService->beginTransaction();

            foreach ($orden->detalles as $detalle) {
                $producto = $detalle->producto;
                $nuevaCantidad = $producto->cantidad + $detalle->cantidad;
                $this->productoRepository->actualizarStock($producto, $nuevaCantidad);
            }

            $this->detalleOrdenRepository->eliminarPorOrden($orden->id);

            $resultado = $this->ordenRepository->eliminar($orden);

            $this->transactionService->commit();

            return $resultado;

        } catch (Exception $e) {
            $this->transactionService->rollBack();
            throw new OrdenException('Error al eliminar la orden: '.$e->getMessage());
        }
    }
}
