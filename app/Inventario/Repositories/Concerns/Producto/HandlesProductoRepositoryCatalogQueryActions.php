<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Concerns\Producto;

use App\Models\Inventario\Producto;
use App\Models\ParametroTema;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

trait HandlesProductoRepositoryCatalogQueryActions
{
    public function obtenerParaCatalogo(array $filtros = []): LengthAwarePaginator
    {
        $query = Producto::select([
            'id',
            'name',
            'codigo_barras',
            'cantidad',
            'imagen',
            'tipo_producto_id',
            'categoria_id',
            'estado_producto_id',
            'created_at',
        ])->where('cantidad', '>', 0);

        if (! empty($filtros['search'])) {
            $query->where('name', 'LIKE', "%{$filtros['search']}%");
        }

        if (! empty($filtros['tipo_producto_id'])) {
            $query->where('tipo_producto_id', $filtros['tipo_producto_id']);
        }

        if (! empty($filtros['categoria_id'])) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }

        if (! empty($filtros['estado_agotado_id'])) {
            $query->where('estado_producto_id', '!=', $filtros['estado_agotado_id']);
        }

        $sortBy = $filtros['sort_by'] ?? 'random';
        switch ($sortBy) {
            case 'stock-asc':
                $query->orderBy('cantidad', 'asc');
                break;
            case 'stock-desc':
                $query->orderBy('cantidad', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'random':
                $query->inRandomOrder();
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage = $filtros['per_page'] ?? 12;

        return $query->paginate($perPage);
    }

    public function buscarParaAjax(array $filtros = []): Collection
    {
        $query = Producto::with([
            'tipoProducto.parametro',
            'unidadMedida.parametro',
            'estado.parametro',
            'contratoConvenio',
            'ambiente',
        ])
            ->where('cantidad', '>', 0)
            ->orderBy('name', 'asc');

        if (! empty($filtros['estado_agotado_id'])) {
            $query->where('estado_producto_id', '!=', $filtros['estado_agotado_id']);
        }

        if (! empty($filtros['search'])) {
            $query->where('name', 'LIKE', "%{$filtros['search']}%");
        }

        if (! empty($filtros['tipo_producto_id'])) {
            $query->where('tipo_producto_id', $filtros['tipo_producto_id']);
        }

        return $query->get();
    }

    public function obtenerTiposProductos(): Collection
    {
        return ParametroTema::with(['parametro', 'tema'])
            ->whereHas('tema', function ($query): void {
                $query->where('name', 'TIPOS DE PRODUCTO');
            })
            ->where('status', 1)
            ->get()
            ->sortBy(function ($tipo) {
                return mb_strtolower($tipo->parametro->name ?? '');
            })
            ->values();
    }

    public function obtenerTodosOrdenadosPorCantidadDesc(): Collection
    {
        return Producto::with([
            'categoria',
            'marca',
            'unidadMedida.parametro',
            'estado.parametro',
            'contratoConvenio',
            'ambiente',
            'proveedor',
        ])
            ->orderBy('cantidad', 'desc')
            ->get();
    }
}
