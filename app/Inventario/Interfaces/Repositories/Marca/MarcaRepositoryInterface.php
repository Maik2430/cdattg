<?php

declare(strict_types=1);

namespace App\Inventario\Interfaces\Repositories\Marca;

use App\Models\Inventario\Marca;
use App\Models\Parametro;
use App\Models\Tema;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MarcaRepositoryInterface
{
    public function obtenerTemaMarcas(): ?Tema;

    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator;

    public function encontrar(int $id): ?Marca;

    public function encontrarMultiples(array $ids): Collection;

    public function encontrarConRelaciones(int $id): ?Parametro;

    public function actualizar(int $id, array $datos): bool;

    public function eliminar(Parametro $marca, int $temaId): bool;

    public function tieneProductos(int $id): bool;
}
