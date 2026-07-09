<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Aprobacion;

use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Orden;
use Illuminate\Support\Collection;

trait HandlesAprobacionConsultaActions
{
    /**
     * Obtiene detalles pendientes de aprobación
     *
     * @return Collection|\Illuminate\Database\Eloquent\Collection
     */
    public function obtenerDetallesPendientes()
    {
        $estadoEnEspera = $this->obtenerEstadoEnEspera();

        if (! $estadoEnEspera || ! isset($estadoEnEspera->id)) {
            return Collection::make([]);
        }

        $detalles = $this->ordenRepository->obtenerDetallesPendientes($estadoEnEspera->id);

        return $detalles instanceof \Illuminate\Database\Eloquent\Collection
            ? $detalles
            : Collection::make($detalles);
    }

    /**
     * Encuentra un detalle de orden con sus relaciones
     */
    public function encontrarDetalleConRelaciones(int $detalleOrdenId): ?DetalleOrden
    {
        return $this->detalleOrdenRepository->encontrarConRelaciones($detalleOrdenId);
    }

    /**
     * Encuentra una orden con detalles y devoluciones
     */
    public function encontrarOrdenConDetallesYDevoluciones(int $ordenId): ?Orden
    {
        return $this->ordenRepository->encontrarConDetallesYDevoluciones($ordenId);
    }
}
