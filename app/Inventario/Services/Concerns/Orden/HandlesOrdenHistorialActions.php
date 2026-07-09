<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Orden;

use App\Exceptions\OrdenException;
use App\Models\Inventario\Aprobacion;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Orden;
use Exception;

trait HandlesOrdenHistorialActions
{
    /**
     * Verifica si una orden tiene devoluciones registradas
     */
    public function tieneDevoluciones(Orden $orden): bool
    {
        return $orden->detalles()->whereHas('devoluciones')->exists();
    }

    /**
     * Elimina del historial las órdenes completamente devueltas sin alterar el stock actual.
     *
     * @return array{eliminadas:int, pendientes:int}
     *
     * @throws OrdenException
     */
    public function vaciarHistorial(): array
    {
        try {
            $this->transactionService->beginTransaction();

            $ordenes = Orden::with(['detalles.devoluciones'])->get();

            $ordenIdsAEliminar = [];
            $detalleIdsAEliminar = [];
            $pendientes = 0;

            foreach ($ordenes as $orden) {
                if ($orden->detalles->isEmpty()) {
                    $ordenIdsAEliminar[] = $orden->id;

                    continue;
                }

                $todosDetallesDevueltos = $orden->detalles->every(
                    static function (DetalleOrden $detalle): bool {
                        return $detalle->estaCompletamenteDevuelto();
                    }
                );

                if ($todosDetallesDevueltos) {
                    $ordenIdsAEliminar[] = $orden->id;
                    foreach ($orden->detalles as $detalle) {
                        $detalleIdsAEliminar[] = $detalle->id;
                    }
                } else {
                    $pendientes++;
                }
            }

            $eliminadas = 0;

            if (! empty($ordenIdsAEliminar)) {
                if (! empty($detalleIdsAEliminar)) {
                    Aprobacion::query()
                        ->whereIn('detalle_orden_id', $detalleIdsAEliminar)
                        ->delete();

                    DetalleOrden::query()
                        ->whereIn('id', $detalleIdsAEliminar)
                        ->delete();
                }

                $eliminadas = Orden::query()
                    ->whereIn('id', $ordenIdsAEliminar)
                    ->delete();
            }

            $this->transactionService->commit();

            return [
                'eliminadas' => (int) $eliminadas,
                'pendientes' => $pendientes,
            ];
        } catch (Exception $e) {
            $this->transactionService->rollBack();
            throw new OrdenException('Error al vaciar el historial de órdenes: '.$e->getMessage());
        }
    }
}
