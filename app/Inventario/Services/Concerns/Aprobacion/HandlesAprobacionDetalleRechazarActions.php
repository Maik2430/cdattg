<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Aprobacion;

use App\Exceptions\AprobacionException;
use App\Models\Inventario\DetalleOrden;
use Illuminate\Support\Facades\Auth;
use Throwable;

trait HandlesAprobacionDetalleRechazarActions
{
    /**
     * Rechaza un detalle de orden
     *
     * @throws AprobacionException
     */
    public function rechazarDetalle(DetalleOrden $detalleOrden, string $motivoRechazo): void
    {
        try {
            $this->transactionService->beginTransaction();

            $estadoEnEspera = $this->obtenerEstadoEnEspera();
            if (! $estadoEnEspera) {
                throw new AprobacionException(self::ERROR_ESTADO_EN_ESPERA_NO_ENCONTRADO);
            }

            $estadoRechazada = $this->obtenerEstadoRechazada();

            $this->validarDetallePendiente($detalleOrden, $estadoEnEspera);

            $this->detalleOrdenRepository->actualizar($detalleOrden, [
                'estado_orden_id' => $estadoRechazada->id,
                'user_update_id' => Auth::id(),
            ]);

            $this->repository->crear([
                'detalle_orden_id' => $detalleOrden->id,
                'estado_aprobacion_id' => $estadoRechazada->id,
                'user_create_id' => Auth::id(),
                'user_update_id' => Auth::id(),
            ]);

            $orden = $detalleOrden->orden;
            $descripcionActualizada = $this->construirDescripcionRechazo($orden->descripcion_orden, $detalleOrden, $motivoRechazo);

            $this->ordenRepository->actualizar($orden, [
                'descripcion_orden' => $descripcionActualizada,
                'user_update_id' => Auth::id(),
            ]);

            $this->notificarRechazo($detalleOrden, $motivoRechazo);

            $this->transactionService->commit();
        } catch (Throwable $e) {
            $this->transactionService->rollBack();
            throw $e instanceof AprobacionException ? $e : new AprobacionException($e->getMessage(), 0, $e);
        }
    }
}
