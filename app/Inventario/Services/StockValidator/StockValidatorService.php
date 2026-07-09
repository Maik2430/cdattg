<?php

declare(strict_types=1);

namespace App\Inventario\Services\StockValidator;

use App\Exceptions\OrdenException;
use App\Inventario\Interfaces\Services\NotificationServiceInterface;
use App\Inventario\Interfaces\Services\StockValidatorServiceInterface;
use App\Models\Inventario\Producto;

/**
 * Servicio para validación de stock y notificaciones
 * Cumple SRP: responsabilidad única de validar stock
 * Cumple OCP: extensible mediante configuración
 */
class StockValidatorService implements StockValidatorServiceInterface
{
    protected NotificationServiceInterface $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Verifica si el stock está bajo el umbral mínimo
     */
    public function estaBajoUmbralMinimo(Producto $producto): bool
    {
        $umbralMinimo = config('inventario.stock.umbral_minimo', 10);

        return $producto->cantidad <= $umbralMinimo;
    }

    /**
     * Verifica si el stock está en nivel crítico
     */
    public function estaNivelCritico(Producto $producto): bool
    {
        $umbralCritico = config('inventario.stock.umbral_critico', 5);

        return $producto->cantidad <= $umbralCritico;
    }

    /**
     * Verifica si hay stock suficiente
     */
    public function hayStockSuficiente(Producto $producto, int $cantidadRequerida): bool
    {
        return $producto->cantidad >= $cantidadRequerida;
    }

    /**
     * Verifica y notifica si el stock cambió a bajo
     */
    public function verificarYNotificarCambioStock(Producto $producto, int $cantidadAnterior): void
    {
        if ($this->debeNotificarCambioStock($producto, $cantidadAnterior)) {
            $umbralMinimo = $this->getUmbralMinimo();
            $this->notificationService->notificarStockBajo(
                $producto,
                $producto->cantidad,
                $umbralMinimo
            );
        }
    }

    private function debeNotificarCambioStock(Producto $producto, int $cantidadAnterior): bool
    {
        // Si las notificaciones están deshabilitadas, no notificar
        if (! config('inventario.stock.notificar_stock_bajo', true)) {
            return false;
        }

        // Si la cantidad no cambió, no notificar
        if ($cantidadAnterior === $producto->cantidad) {
            return false;
        }

        $umbralMinimo = $this->getUmbralMinimo();

        // Notificar en dos casos:
        // 1. El stock cruzó el umbral (era mayor, ahora es menor o igual)
        // 2. El stock ya estaba bajo y disminuyó aún más
        if ($cantidadAnterior > $umbralMinimo && $this->estaBajoUmbralMinimo($producto)) {
            return true; // Caso 1: cruzó el umbral
        }

        if ($cantidadAnterior <= $umbralMinimo && $producto->cantidad < $cantidadAnterior) {
            return true; // Caso 2: ya estaba bajo y disminuyó
        }

        return false;
    }

    /**
     * Obtiene el umbral mínimo configurado
     */
    public function getUmbralMinimo(): int
    {
        return config('inventario.stock.umbral_minimo', 10);
    }

    /**
     * Obtiene el umbral crítico configurado
     */
    public function getUmbralCritico(): int
    {
        return config('inventario.stock.umbral_critico', 5);
    }

    /**
     * Calcula el porcentaje de stock disponible
     */
    public function calcularPorcentajeStock(Producto $producto, int $stockMaximo): float
    {
        if ($stockMaximo <= 0) {
            return 0.0;
        }

        return ($producto->cantidad / $stockMaximo) * 100;
    }

    /**
     * Obtiene nivel de stock (crítico, bajo, normal, alto)
     */
    public function obtenerNivelStock(Producto $producto): string
    {
        $nivel = 'alto';

        if ($this->estaNivelCritico($producto)) {
            $nivel = 'critico';
        } elseif ($this->estaBajoUmbralMinimo($producto)) {
            $nivel = 'bajo';
        } else {
            $umbralMinimo = $this->getUmbralMinimo();
            if ($producto->cantidad <= ($umbralMinimo * 2)) {
                $nivel = 'normal';
            }
        }

        return $nivel;
    }

    /**
     * Valida que haya stock suficiente, lanza excepción si no
     *
     * @throws OrdenException
     */
    public function validarStockSuficiente(Producto $producto, int $cantidadRequerida): void
    {
        if (! $this->hayStockSuficiente($producto, $cantidadRequerida)) {
            throw new OrdenException(
                "Stock insuficiente para '{$producto->name}'. ".
                "Disponible: {$producto->cantidad}, Solicitado: {$cantidadRequerida}"
            );
        }
    }
}
