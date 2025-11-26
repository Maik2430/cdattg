<?php

declare(strict_types=1);

namespace App\Repositories\Inventario;

use App\Models\Inventario\Proveedor;
use App\Core\Traits\HasCache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProveedorRepository
{
    use HasCache;

    public function __construct()
    {
        $this->cacheType = 'proveedores';
        $this->cacheTags = ['proveedores', 'inventario'];
    }

    /**
     * Obtiene proveedores con filtros y relaciones
     *
     * @param array $filtros
     * @return LengthAwarePaginator
     */
    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $query = Proveedor::with([
            'userCreate.persona',
            'userUpdate.persona',
            'estado.parametro',
            'departamento',
            'municipio'
        ])
        ->withCount('contratosConvenios')
        ->latest();

        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search) {
                $q->where('proveedor', 'LIKE', "%{$search}%")
                    ->orWhere('nit', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('telefono', 'LIKE', "%{$search}%")
                    ->orWhere('contacto', 'LIKE', "%{$search}%")
                    ->orWhereHas('departamento', function ($departamentoQuery) use ($search) {
                        $departamentoQuery->where('departamento', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('municipio', function ($municipioQuery) use ($search) {
                        $municipioQuery->where('municipio', 'LIKE', "%{$search}%");
                    });
            });
        }

        $perPage = $filtros['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

    /**
     * Encuentra un proveedor por ID con relaciones
     *
     * @param int $id
     * @return Proveedor|null
     */
    public function encontrarConRelaciones(int $id): ?Proveedor
    {
        return Proveedor::with([
            'contratosConvenios',
            'userCreate.persona',
            'userUpdate.persona',
            'estado.parametro',
            'departamento',
            'municipio'
        ])->find($id);
    }

    /**
     * Crea un nuevo proveedor
     *
     * @param array $datos
     * @return Proveedor
     */
    public function crear(array $datos): Proveedor
    {
        $this->flushCache();
        return Proveedor::create($datos);
    }

    /**
     * Actualiza un proveedor
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        $this->flushCache();
        return Proveedor::where('id', $id)->update($datos);
    }

    /**
     * Elimina un proveedor
     *
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool
    {
        $this->flushCache();
        return Proveedor::destroy($id);
    }

    /**
     * Verifica si un proveedor tiene contratos asociados
     *
     * @param int $id
     * @return bool
     */
    public function tieneContratos(int $id): bool
    {
        $proveedor = Proveedor::withCount('contratosConvenios')->find($id);
        return $proveedor && $proveedor->contratos_convenios_count > 0;
    }

    /**
     * Verifica si un proveedor tiene productos asociados
     *
     * @param int $id
     * @return bool
     */
    public function tieneProductos(int $id): bool
    {
        $proveedor = Proveedor::withCount('productos')->find($id);
        return $proveedor && $proveedor->productos_count > 0;
    }
}

