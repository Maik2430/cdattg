<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Orden;

use App\Exceptions\OrdenException;
use App\Models\Inventario\Orden;
use Exception;
use Illuminate\Support\Facades\Auth;

trait HandlesOrdenCreacionActions
{
    /**
     * Crea una nueva orden con sus detalles
     *
     * @throws OrdenException
     */
    public function crear(array $datos, int $userId): Orden
    {
        try {
            $this->transactionService->beginTransaction();

            $orden = $this->ordenRepository->crear([
                'descripcion_orden' => $datos['descripcion_orden'],
                'tipo_orden_id' => $datos['tipo_orden_id'],
                'fecha_devolucion' => $datos['fecha_devolucion'] ?? null,
                'user_create_id' => $userId,
                'user_update_id' => $userId,
            ]);

            foreach ($datos['productos'] as $productoData) {
                $this->procesarDetalleOrden($orden, $productoData, $userId);
            }

            $this->transactionService->commit();

            return $orden;

        } catch (Exception $e) {
            $this->transactionService->rollBack();
            throw new OrdenException('Error al crear la orden: '.$e->getMessage());
        }
    }

    /**
     * Crea una orden de préstamo/salida desde carrito
     *
     * @throws OrdenException
     */
    public function crearDesdeCarrito(array $datos, int $userId): Orden
    {
        try {
            $this->transactionService->beginTransaction();

            $carrito = json_decode($datos['carrito'], true);

            if (empty($carrito) || ! is_array($carrito)) {
                throw new OrdenException('El carrito está vacío.');
            }

            $tipoMap = [
                'prestamo' => 'PRÉSTAMO',
                'salida' => 'SALIDA',
            ];

            $codigoTipoOrden = $tipoMap[$datos['tipo']] ?? strtoupper($datos['tipo']);
            $parametroTipoOrden = $this->obtenerParametroTipoOrden($codigoTipoOrden);
            $estadoEnEspera = $this->obtenerEstadoEnEspera();

            $usuario = Auth::user();
            $descripcionDetallada = $this->generarDescripcionOrden($datos, $usuario);

            $orden = $this->ordenRepository->crear([
                'descripcion_orden' => $descripcionDetallada,
                'tipo_orden_id' => $parametroTipoOrden->id,
                'fecha_devolucion' => $datos['tipo'] === 'prestamo' ? $datos['fecha_devolucion'] : null,
                'user_create_id' => $userId,
                'user_update_id' => $userId,
            ]);

            foreach ($carrito as $item) {
                $productoId = $item['id'] ?? $item['producto_id'] ?? null;
                $cantidad = (int) ($item['quantity'] ?? $item['cantidad'] ?? 1);

                if (! $productoId) {
                    continue;
                }

                $producto = $this->productoRepository->encontrar((int) $productoId);

                if (! $producto) {
                    throw new OrdenException("Producto con ID {$productoId} no encontrado.");
                }

                $this->stockValidator->validarStockSuficiente($producto, $cantidad);

                $this->detalleOrdenRepository->crear([
                    'orden_id' => $orden->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'estado_orden_id' => $estadoEnEspera->id,
                    'user_create_id' => $userId,
                    'user_update_id' => $userId,
                ]);
            }

            $this->notificarNuevaOrden($orden);

            $this->transactionService->commit();

            session()->forget('carrito_data');

            return $orden;

        } catch (OrdenException $e) {
            $this->transactionService->rollBack();
            throw $e;
        } catch (Exception $e) {
            $this->transactionService->rollBack();
            throw new OrdenException('Error al crear la orden: '.$e->getMessage());
        }
    }
}
