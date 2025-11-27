<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces\Inventario;

use App\Models\Inventario\Producto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductoRepositoryInterface
{
    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator;
    public function encontrarConRelaciones(int $id): ?Producto;
    public function buscarPorCodigoBarras(string $codigo): ?Producto;
    public function obtenerParaCatalogo(array $filtros = []): LengthAwarePaginator;
    public function buscarParaAjax(array $filtros = []): Collection;
    public function obtenerTiposProductos(): Collection;
    public function invalidarCache(): void;
}
