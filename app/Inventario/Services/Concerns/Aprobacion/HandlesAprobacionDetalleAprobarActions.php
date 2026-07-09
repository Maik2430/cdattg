<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Aprobacion;

use App\Exceptions\AprobacionException;
use App\Models\Inventario\DetalleOrden;
use App\Models\ParametroTema;
use Illuminate\Support\Facades\Auth;
use Throwable;

trait HandlesAprobacionDetalleAprobarActions
{
    /**
     * Aprueba un detalle de orden
     *
     * @throws AprobacionException
     */
    public function aprobarDetalle(DetalleOrden $detalleOrden): void
    {
        try {
            $this->transactionService->beginTransaction();

            $estadoEnEspera = $this->obtenerEstadoEnEspera();
            if (! $estadoEnEspera) {
                throw new AprobacionException(self::ERROR_ESTADO_EN_ESPERA_NO_ENCONTRADO);
            }

            $estadoAprobada = $this->obtenerEstadoAprobada();

            $this->validarDetallePendiente($detalleOrden, $estadoEnEspera);

            $producto = $detalleOrden->producto;

            $this->stockValidator->validarStockSuficiente($producto, $detalleOrden->cantidad);

            $this->detalleOrdenRepository->actualizar($detalleOrden, [
                'estado_orden_id' => $estadoAprobada->id,
                'user_update_id' => Auth::id(),
            ]);

            $this->repository->crear([
                'detalle_orden_id' => $detalleOrden->id,
                'estado_aprobacion_id' => $estadoAprobada->id,
                'user_create_id' => Auth::id(),
                'user_update_id' => Auth::id(),
            ]);

            $cantidadAnterior = $producto->cantidad;
            $nuevaCantidad = $cantidadAnterior - $detalleOrden->cantidad;
            $this->productoRepository->actualizarStock($producto, $nuevaCantidad);
            $this->stockValidator->verificarYNotificarCambioStock($producto, $cantidadAnterior);
            $this->productoRepository->actualizar($producto, ['user_update_id' => Auth::id()]);

            $this->notificarAprobacion($detalleOrden);

            $this->transactionService->commit();
        } catch (Throwable $e) {
            $this->transactionService->rollBack();
            throw $e instanceof AprobacionException ? $e : new AprobacionException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Valida que el detalle esté pendiente de aprobación
     *
     * @throws AprobacionException
     */
    private function validarDetallePendiente(DetalleOrden $detalleOrden, ?ParametroTema $estadoEnEspera): void
    {
        if (! $estadoEnEspera || $detalleOrden->estado_orden_id != $estadoEnEspera->id) {
            throw new AprobacionException('Esta solicitud no está pendiente de aprobación.');
        }

        if ($detalleOrden->aprobacion) {
            throw new AprobacionException('Esta solicitud ya fue procesada anteriormente.');
        }
    }
}
