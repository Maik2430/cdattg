<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Devolucion;

use App\Inventario\Interfaces\Repositories\Devolucion\DevolucionRepositoryInterface;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Devolucion;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DevolucionRepository implements DevolucionRepositoryInterface
{
    /**
     * Obtiene préstamos pendientes de devolución
     */
    public function obtenerPrestamosPendientes(int $estadoAprobadaId, ?int $userId = null): LengthAwarePaginator
    {
        $prestamos = DetalleOrden::with(['orden.tipoOrden.parametro', 'producto', 'devoluciones'])
            ->whereHas('orden', function ($query) use ($userId): void {
                $query->whereNotNull('fecha_devolucion');

                if ($userId !== null) {
                    $query->where('user_create_id', $userId);
                }
            })
            ->where('estado_orden_id', $estadoAprobadaId)
            ->get()
            ->filter(function ($detalle): bool {
                return ! $detalle->estaCompletamenteDevuelto();
            });

        return $this->paginacionManual($prestamos, 10);
    }

    /**
     * Obtiene historial de devoluciones
     */
    public function obtenerHistorial(?int $userId = null): LengthAwarePaginator
    {
        $query = Devolucion::with(['detalleOrden.producto', 'detalleOrden.orden', 'userCreate'])
            ->orderBy('fecha_devolucion', 'desc');

        if ($userId !== null) {
            $query->whereHas('detalleOrden.orden', function ($q) use ($userId): void {
                $q->where('user_create_id', $userId);
            });
        }

        return $query->paginate(20);
    }

    /**
     * Obtiene devolución con relaciones
     */
    public function encontrarConRelaciones(int $id): ?Devolucion
    {
        return Devolucion::with([
            'detalleOrden.producto',
            'detalleOrden.orden',
            'userCreate',
            'userUpdate',
        ])->find($id);
    }

    /**
     * Obtiene préstamos activos del usuario
     */
    public function obtenerPrestamosActivosUsuario(int $userId, int $estadoAprobadaId): LengthAwarePaginator
    {
        $prestamos = DetalleOrden::with(['orden.tipoOrden.parametro', 'producto', 'devoluciones'])
            ->whereHas('orden', function ($query) use ($userId): void {
                $query->where('user_create_id', $userId)
                    ->whereNotNull('fecha_devolucion');
            })
            ->where('estado_orden_id', $estadoAprobadaId)
            ->get()
            ->filter(function ($detalle): bool {
                return ! $detalle->estaCompletamenteDevuelto();
            });

        return $this->paginacionManual($prestamos, 10);
    }

    /**
     * Obtiene historial de préstamos del usuario
     */
    public function obtenerHistorialPrestamosUsuario(int $userId): LengthAwarePaginator
    {
        return DetalleOrden::with(['orden.tipoOrden', 'producto', 'devoluciones'])
            ->whereHas('orden', function ($query) use ($userId): void {
                $query->where('user_create_id', $userId)
                    ->whereNotNull('fecha_devolucion');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    /**
     * Crea paginación manual para colecciones filtradas
     */
    private function paginacionManual(Collection $items, int $perPage): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        $paginatedItems = $items->forPage((int) $page, $perPage)->values();

        try {
            $path = request()->url();
        } catch (Exception $e) {
            $path = route('inventario.devoluciones.index');
        }

        if (! $path) {
            $path = route('inventario.devoluciones.index');
        }

        $query = request()->query() ?? [];

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $items->count(),
            $perPage,
            (int) $page,
            ['path' => $path, 'query' => $query]
        );
    }
}
