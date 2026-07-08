<?php

declare(strict_types=1);

namespace App\Models\Concerns\Producto;

use App\Exceptions\StockException;

trait ManagesProductoStock
{
    public function tieneStockDisponible(int $cantidadRequerida): bool
    {
        return $this->cantidad >= $cantidadRequerida;
    }

    public function descontarStock(int $cantidad): self
    {
        if (! $this->tieneStockDisponible($cantidad)) {
            throw new StockException("Stock insuficiente. Disponible: {$this->cantidad}, Requerido: {$cantidad}");
        }

        $this->cantidad -= $cantidad;
        $this->save();

        return $this;
    }

    public function devolverStock(int $cantidad): self
    {
        $this->cantidad += $cantidad;
        $this->save();

        return $this;
    }

    public function esConsumible(): bool
    {
        $this->loadMissing(['tipoProducto.parametro']);

        $tipo = $this->tipoProducto;
        if ($tipo === null || $tipo->parametro === null) {
            return false;
        }

        return strtoupper($tipo->parametro->name) === 'CONSUMIBLE';
    }

    public function getPorcentajeStock(int $stockMaximo = 100): float
    {
        if ($this->cantidad <= 0) {
            return 0;
        }

        return round(($this->cantidad / $stockMaximo) * 100, 2);
    }

    public function getEstadoStock(): string
    {
        $cantidad = $this->cantidad;

        return match (true) {
            $cantidad <= 5 => 'critico',
            $cantidad <= 10 => 'bajo',
            $cantidad <= 20 => 'medio',
            default => 'normal',
        };
    }

    public function getBadgeStock(): string
    {
        $estado = $this->getEstadoStock();
        $clases = [
            'critico' => 'badge-danger',
            'bajo' => 'badge-warning',
            'medio' => 'badge-info',
            'normal' => 'badge-success',
        ];

        $textos = [
            'critico' => 'CRÍTICO',
            'bajo' => 'BAJO',
            'medio' => 'MEDIO',
            'normal' => 'NORMAL',
        ];

        $clase = $clases[$estado] ?? 'badge-secondary';
        $texto = $textos[$estado] ?? 'N/A';

        return "<span class='badge {$clase}'>{$texto}: {$this->cantidad}</span>";
    }
}
