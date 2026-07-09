<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Concerns\Producto;

use App\Models\Inventario\Producto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait HandlesProductoRepositoryFilterQueryActions
{
    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $query = Producto::with([
            'tipoProducto.parametro',
            'unidadMedida.parametro',
            'estado.parametro',
            'contratoConvenio',
            'ambiente',
            'proveedor',
        ]);

        if (! empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('codigo_barras', 'LIKE', "%{$search}%")
                    ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($filtros['tipo_producto_id'])) {
            $query->where('tipo_producto_id', $filtros['tipo_producto_id']);
        }

        if (! empty($filtros['categoria_id'])) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }

        if (! empty($filtros['marca_id'])) {
            $query->where('marca_id', $filtros['marca_id']);
        }

        if (! empty($filtros['estado_producto_id'])) {
            $estadoFiltro = $filtros['estado_producto_id'];

            if ($estadoFiltro === 'bajo_stock') {
                $query->where('cantidad', '>', 0)
                    ->where('cantidad', '<=', 5);
            } elseif ($estadoFiltro === 'solo_agotado') {
                $query->where('cantidad', '<=', 0);
            } else {
                $query->where('estado_producto_id', $estadoFiltro);
            }
        }

        if (isset($filtros['stock_minimo'])) {
            $query->where('cantidad', '>=', $filtros['stock_minimo']);
        }

        if (isset($filtros['solo_con_stock'])) {
            $query->where('cantidad', '>', 0);
        }

        $perPage = $filtros['per_page'] ?? 10;

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function encontrarConRelaciones(int $id): ?Producto
    {
        return Producto::with([
            'tipoProducto.parametro',
            'unidadMedida.parametro',
            'estado.parametro',
            'categoria',
            'marca',
            'contratoConvenio',
            'ambiente',
            'proveedor',
        ])->find($id);
    }
}
