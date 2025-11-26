<?php

declare(strict_types=1);

namespace App\Repositories\Inventario;

use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;
use App\Core\Traits\HasCache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MarcaRepository
{
    use HasCache;

    private const TEMA_MARCAS = 'MARCAS';

    public function __construct()
    {
        $this->cacheType = 'marcas';
        $this->cacheTags = ['marcas', 'inventario'];
    }

    /**
     * Obtiene el tema de marcas
     *
     * @return Tema|null
     */
    public function obtenerTemaMarcas(): ?Tema
    {
        return Tema::where('name', self::TEMA_MARCAS)->first();
    }

    /**
     * Obtiene marcas con filtros
     *
     * @param array $filtros
     * @return LengthAwarePaginator
     */
    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $temaMarcas = $this->obtenerTemaMarcas();

        if (!$temaMarcas) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        $query = $temaMarcas->parametros()
            ->with(['userCreate.persona', 'userUpdate.persona'])
            ->wherePivot('status', 1);

        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search) {
                $q->where('parametros.name', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $filtros['per_page'] ?? 10;
        $marcas = $query->paginate($perPage);

        // Cargar conteo de productos para cada marca
        foreach ($marcas as $marca) {
            $marca->productos_count = \App\Models\Inventario\Producto::where('marca_id', $marca->id)->count();
        }

        return $marcas;
    }

    /**
     * Encuentra una marca por ID con relaciones
     *
     * @param int $id
     * @return Parametro|null
     */
    public function encontrarConRelaciones(int $id): ?Parametro
    {
        return Parametro::with(['userCreate.persona', 'userUpdate.persona'])->find($id);
    }

    /**
     * Crea una nueva marca
     *
     * @param array $datos
     * @return Parametro
     */
    public function crear(array $datos): Parametro
    {
        $this->flushCache();
        return Parametro::create($datos);
    }

    /**
     * Actualiza una marca
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        $this->flushCache();
        return Parametro::where('id', $id)->update($datos);
    }

    /**
     * Elimina una marca
     *
     * @param Parametro $marca
     * @param int $temaId
     * @return bool
     */
    public function eliminar(Parametro $marca, int $temaId): bool
    {
        $this->flushCache();
        
        // Desvincular del tema "MARCAS"
        ParametroTema::where('parametro_id', $marca->id)
            ->where('tema_id', $temaId)
            ->delete();

        return $marca->delete();
    }

    /**
     * Verifica si una marca tiene productos asociados
     *
     * @param int $id
     * @return bool
     */
    public function tieneProductos(int $id): bool
    {
        return \App\Models\Inventario\Producto::where('marca_id', $id)->exists();
    }
}

