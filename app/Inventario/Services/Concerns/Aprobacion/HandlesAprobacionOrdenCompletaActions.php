<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Aprobacion;

use App\Exceptions\AprobacionException;
use App\Models\Inventario\Orden;
use App\Notifications\OrdenAprobadaNotification;
use App\Notifications\OrdenRechazadaNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Throwable;

trait HandlesAprobacionOrdenCompletaActions
{
    /**
     * Aprueba toda una orden completa
     *
     * @throws AprobacionException
     */
    public function aprobarOrdenCompleta(Orden $orden): void
    {
        try {
            $this->transactionService->beginTransaction();

            $estadoEnEspera = $this->obtenerEstadoEnEspera();
            if (! $estadoEnEspera) {
                throw new AprobacionException(self::ERROR_ESTADO_EN_ESPERA_NO_ENCONTRADO);
            }

            $estadoAprobada = $this->obtenerEstadoAprobada();

            $detallesPendientes = $orden->detalles->where('estado_orden_id', $estadoEnEspera->id);

            if ($detallesPendientes->isEmpty()) {
                throw new AprobacionException('No hay productos pendientes de aprobación en esta orden.');
            }

            foreach ($detallesPendientes as $detalle) {
                $this->stockValidator->validarStockSuficiente($detalle->producto, $detalle->cantidad);
            }

            foreach ($detallesPendientes as $detalle) {
                $this->detalleOrdenRepository->actualizar($detalle, [
                    'estado_orden_id' => $estadoAprobada->id,
                    'user_update_id' => Auth::id(),
                ]);

                $this->repository->crear([
                    'detalle_orden_id' => $detalle->id,
                    'estado_aprobacion_id' => $estadoAprobada->id,
                    'user_create_id' => Auth::id(),
                    'user_update_id' => Auth::id(),
                ]);

                $cantidadAnterior = $detalle->producto->cantidad;
                $nuevaCantidad = $cantidadAnterior - $detalle->cantidad;
                $this->productoRepository->actualizarStock($detalle->producto, $nuevaCantidad);
                $this->stockValidator->verificarYNotificarCambioStock($detalle->producto, $cantidadAnterior);
                $this->productoRepository->actualizar($detalle->producto, ['user_update_id' => Auth::id()]);
            }

            $solicitante = $orden->userCreate;
            if ($solicitante) {
                foreach ($detallesPendientes as $detalle) {
                    Notification::send($solicitante, new OrdenAprobadaNotification($detalle, Auth::user()));
                }
            }

            $this->transactionService->commit();
        } catch (Throwable $e) {
            $this->transactionService->rollBack();
            throw $e instanceof AprobacionException ? $e : new AprobacionException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Rechaza toda una orden completa
     *
     * @throws AprobacionException
     */
    public function rechazarOrdenCompleta(Orden $orden, string $motivoRechazo): void
    {
        try {
            $this->transactionService->beginTransaction();

            $estadoEnEspera = $this->obtenerEstadoEnEspera();
            if (! $estadoEnEspera) {
                throw new AprobacionException(self::ERROR_ESTADO_EN_ESPERA_NO_ENCONTRADO);
            }

            $estadoRechazada = $this->obtenerEstadoRechazada();

            $detallesPendientes = $orden->detalles->where('estado_orden_id', $estadoEnEspera->id);

            if ($detallesPendientes->isEmpty()) {
                throw new AprobacionException('No hay productos pendientes de aprobación en esta orden.');
            }

            foreach ($detallesPendientes as $detalle) {
                $this->detalleOrdenRepository->actualizar($detalle, [
                    'estado_orden_id' => $estadoRechazada->id,
                    'user_update_id' => Auth::id(),
                ]);

                $this->repository->crear([
                    'detalle_orden_id' => $detalle->id,
                    'estado_aprobacion_id' => $estadoRechazada->id,
                    'user_create_id' => Auth::id(),
                    'user_update_id' => Auth::id(),
                ]);
            }

            $descripcionActualizada = $this->construirDescripcionRechazo($orden->descripcion_orden, $detallesPendientes->first(), $motivoRechazo);
            $descripcionActualizada = str_replace('--- SOLICITUD RECHAZADA ---', '--- ORDEN RECHAZADA COMPLETA ---', $descripcionActualizada);

            $this->ordenRepository->actualizar($orden, [
                'descripcion_orden' => $descripcionActualizada,
                'user_update_id' => Auth::id(),
            ]);

            $solicitante = $orden->userCreate;
            if ($solicitante) {
                foreach ($detallesPendientes as $detalle) {
                    Notification::send($solicitante, new OrdenRechazadaNotification($detalle, Auth::user(), $motivoRechazo));
                }
            }

            $this->transactionService->commit();
        } catch (Throwable $e) {
            $this->transactionService->rollBack();
            throw $e instanceof AprobacionException ? $e : new AprobacionException($e->getMessage(), 0, $e);
        }
    }
}
