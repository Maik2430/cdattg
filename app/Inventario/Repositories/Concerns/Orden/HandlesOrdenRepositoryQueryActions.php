<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Concerns\Orden;

use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Orden;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

trait HandlesOrdenRepositoryQueryActions
{
    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $query = Orden::with([
            'tipoOrden.parametro',
            'userCreate',
            'detalles.producto',
            'detalles.estadoOrden.parametro',
        ])->latest();

        if (! empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('descripcion_orden', 'LIKE', "%{$search}%")
                    ->orWhereHas('userCreate', function ($userQuery) use ($search): void {
                        $userQuery->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('tipoOrden.parametro', function ($tipoQuery) use ($search): void {
                        $tipoQuery->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('detalles.producto', function ($productoQuery) use ($search): void {
                        $productoQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('codigo_barras', 'LIKE', "%{$search}%");
                    });

                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        if (! empty($filtros['tipo_orden_id'])) {
            $query->where('tipo_orden_id', $filtros['tipo_orden_id']);
        }

        if (! empty($filtros['estado_id'])) {
            $query->whereHas('detalles', function ($q) use ($filtros): void {
                $q->where('estado_orden_id', $filtros['estado_id']);
            });
        }

        if (! empty($filtros['user_id'])) {
            $query->where('user_create_id', $filtros['user_id']);
        }

        $perPage = $filtros['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    public function obtenerPendientes(int $estadoEnEsperaId, ?int $userId = null): LengthAwarePaginator
    {
        return $this->obtenerPorEstadoDetalle($estadoEnEsperaId, $userId);
    }

    public function obtenerCompletadas(int $estadoAprobadaId, ?int $userId = null): LengthAwarePaginator
    {
        return $this->obtenerPorEstadoDetalle($estadoAprobadaId, $userId);
    }

    public function obtenerRechazadas(int $estadoRechazadaId, ?int $userId = null): LengthAwarePaginator
    {
        return $this->obtenerPorEstadoDetalle($estadoRechazadaId, $userId);
    }

    public function encontrarConRelaciones(int $id): ?Orden
    {
        return Orden::with([
            'tipoOrden.parametro',
            'userCreate',
            'detalles.producto',
            'detalles.estadoOrden.parametro',
            'detalles.aprobacion.aprobador',
        ])->find($id);
    }

    public function encontrarConDetallesYDevoluciones(int $id): ?Orden
    {
        return Orden::with(['detalles.producto', 'detalles.devoluciones'])->find($id);
    }

    public function obtenerDetallesPendientes(int $estadoEnEsperaId): Collection
    {
        return DetalleOrden::with([
            'orden.tipoOrden.parametro',
            'orden.userCreate',
            'producto',
            'estadoOrden.parametro',
            'aprobacion',
        ])
            ->where('estado_orden_id', $estadoEnEsperaId)
            ->whereDoesntHave('aprobacion')
            ->latest()
            ->get();
    }

    private function obtenerPorEstadoDetalle(int $estadoId, ?int $userId = null): LengthAwarePaginator
    {
        $query = Orden::with([
            'tipoOrden.parametro',
            'userCreate',
            'detalles.producto',
            'detalles.estadoOrden.parametro',
        ])
            ->whereHas('detalles', function ($q) use ($estadoId): void {
                $q->where('estado_orden_id', $estadoId);
            });

        if ($userId !== null) {
            $query->where('user_create_id', $userId);
        }

        return $query->latest()->paginate(15);
    }
}
