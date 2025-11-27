<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces\Inventario;

use App\Models\Parametro;
use App\Models\Tema;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MarcaRepositoryInterface
{
    public function obtenerTemaMarcas(): ?Tema;
    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator;
    public function encontrarConRelaciones(int $id): ?Parametro;
    public function actualizar(int $id, array $datos): bool;
    public function eliminar(Parametro $marca, int $temaId): bool;
    public function tieneProductos(int $id): bool;
}

