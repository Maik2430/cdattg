<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Inventario;

use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;
use App\Models\Inventario\Producto;
use App\Repositories\Interfaces\Inventario\CategoriaRepositoryInterface;
use App\Core\Traits\HasCache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoriaRepository implements CategoriaRepositoryInterface
{
    use HasCache;

    private const TEMA_CATEGORIAS = 'CATEGORIAS';

    public function __construct()
    {
        $this->cacheType = 'categorias';
        $this->cacheTags = ['categorias', 'inventario'];
    }

    /**
     * Obtiene el tema de categorías
     *
     * @return Tema|null
     */
    public function obtenerTemaCategorias(): ?Tema
    {
        return Tema::where('name', self::TEMA_CATEGORIAS)->first();
    }

    /**
     * Obtiene categorías con filtros
     *
     * @param array $filtros
     * @return LengthAwarePaginator
     */
    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $temaCategorias = $this->obtenerTemaCategorias();

        if (!$temaCategorias) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        $query = $temaCategorias->parametros()
            ->with(['userCreate.persona', 'userUpdate.persona'])
            ->wherePivot('status', 1);

        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search) {
                $q->where('parametros.name', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $filtros['per_page'] ?? 10;
        $categorias = $query->paginate($perPage);

        // Cargar conteo de productos para cada categoría
        foreach ($categorias as $categoria) {
            $categoria->productos_count = Producto::where('categoria_id', $categoria->id)->count();
        }

        return $categorias;
    }

    /**
     * Encuentra una categoría por ID con relaciones
     *
     * @param int $id
     * @return Parametro|null
     */
    public function encontrarConRelaciones(int $id): ?Parametro
    {
        return Parametro::with(['userCreate.persona', 'userUpdate.persona'])->find($id);
    }

    /**
     * Actualiza una categoría
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
     * Elimina una categoría
     *
     * @param Parametro $categoria
     * @param int $temaId
     * @return bool
     */
    public function eliminar(Parametro $categoria, int $temaId): bool
    {
        $this->flushCache();
        
        // Desvincular del tema "CATEGORIAS"
        ParametroTema::where('parametro_id', $categoria->id)
            ->where('tema_id', $temaId)
            ->delete();

        return $categoria->delete();
    }

    /**
     * Verifica si una categoría tiene productos asociados
     *
     * @param int $id
     * @return bool
     */
    public function tieneProductos(int $id): bool
    {
        return Producto::where('categoria_id', $id)->exists();
    }
}

