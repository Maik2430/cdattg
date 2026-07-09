<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Marca;

use App\Inventario\Interfaces\Repositories\Marca\MarcaRepositoryInterface;
use App\Models\Inventario\Marca;
use App\Models\Inventario\Producto;
use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MarcaRepository implements MarcaRepositoryInterface
{
    private const TEMA_MARCAS = 'MARCAS';

    /**
     * Obtiene el tema de marcas
     */
    public function obtenerTemaMarcas(): ?Tema
    {
        return Tema::where('name', self::TEMA_MARCAS)->first();
    }

    /**
     * Obtiene marcas con filtros
     */
    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $temaMarcas = $this->obtenerTemaMarcas();

        if (! $temaMarcas) {
            return new LengthAwarePaginator([], 0, 10);
        }

        $query = $temaMarcas->parametros()
            ->with(['userCreate.persona', 'userUpdate.persona'])
            ->wherePivot('status', 1);

        if (! empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('parametros.name', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $filtros['per_page'] ?? 10;
        $marcas = $query->paginate($perPage);

        // Cargar conteo de productos para cada marca
        foreach ($marcas as $marca) {
            $marca->productos_count = Producto::where('marca_id', $marca->id)->count();
        }

        return $marcas;
    }

    /**
     * Encuentra una marca por ID
     */
    public function encontrar(int $id): ?Marca
    {
        return Marca::find($id);
    }

    /**
     * Encuentra múltiples marcas por IDs
     */
    public function encontrarMultiples(array $ids): Collection
    {
        return Marca::whereIn('id', $ids)->get()->keyBy('id');
    }

    /**
     * Encuentra una marca por ID con relaciones
     */
    public function encontrarConRelaciones(int $id): ?Parametro
    {
        return Parametro::with(['userCreate.persona', 'userUpdate.persona'])->find($id);
    }

    /**
     * Actualiza una marca
     */
    public function actualizar(int $id, array $datos): bool
    {
        return Parametro::where('id', $id)->update($datos) > 0;
    }

    /**
     * Elimina una marca
     */
    public function eliminar(Parametro $marca, int $temaId): bool
    {
        // Desvincular del tema "MARCAS"
        ParametroTema::where('parametro_id', $marca->id)
            ->where('tema_id', $temaId)
            ->delete();

        return $marca->delete();
    }

    /**
     * Verifica si una marca tiene productos asociados
     */
    public function tieneProductos(int $id): bool
    {
        return Producto::where('marca_id', $id)->exists();
    }
}
