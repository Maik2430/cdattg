<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Proveedor;

use App\Inventario\Interfaces\Repositories\Proveedor\ProveedorRepositoryInterface;
use App\Models\Inventario\Proveedor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProveedorRepository implements ProveedorRepositoryInterface
{
    /**
     * Obtiene todos los proveedores
     */
    public function obtenerTodos(): Collection
    {
        return Proveedor::orderBy('name')->get();
    }

    /**
     * Obtiene proveedores con filtros y relaciones
     */
    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $query = Proveedor::with([
            'userCreate.persona',
            'userUpdate.persona',
            'estado.parametro',
            'pais',
            'departamento',
            'municipio',
            'persona',
        ])
            ->withCount('contratosConvenios')
            ->latest();

        if (! empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('nit', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('telefono', 'LIKE', "%{$search}%")
                    ->orWhereHas('persona', function ($personaQuery) use ($search): void {
                        $personaQuery->where('primer_nombre', 'LIKE', "%{$search}%")
                            ->orWhere('segundo_nombre', 'LIKE', "%{$search}%")
                            ->orWhere('primer_apellido', 'LIKE', "%{$search}%")
                            ->orWhere('segundo_apellido', 'LIKE', "%{$search}%")
                            ->orWhere('telefono', 'LIKE', "%{$search}%")
                            ->orWhere('celular', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('departamento', function ($departamentoQuery) use ($search): void {
                        $departamentoQuery->where('departamento', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('municipio', function ($municipioQuery) use ($search): void {
                        $municipioQuery->where('municipio', 'LIKE', "%{$search}%");
                    });
            });
        }

        $perPage = $filtros['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Encuentra un proveedor por ID con relaciones
     */
    public function encontrarConRelaciones(int $id): ?Proveedor
    {
        return Proveedor::with([
            'contratosConvenios',
            'userCreate.persona',
            'userUpdate.persona',
            'estado.parametro',
            'pais',
            'departamento',
            'municipio',
            'persona',
        ])->find($id);
    }

    /**
     * Crea un nuevo proveedor
     */
    public function crear(array $datos): Proveedor
    {
        return Proveedor::create($datos);
    }

    /**
     * Actualiza un proveedor
     */
    public function actualizar(int $id, array $datos): bool
    {
        return Proveedor::where('id', $id)->update($datos) > 0;
    }

    /**
     * Elimina un proveedor
     */
    public function eliminar(int $id): bool
    {
        return Proveedor::destroy($id) > 0;
    }

    /**
     * Verifica si un proveedor tiene contratos asociados
     */
    public function tieneContratos(int $id): bool
    {
        return Proveedor::where('id', $id)
            ->whereHas('contratosConvenios')
            ->exists();
    }

    /**
     * Verifica si un proveedor tiene productos asociados
     */
    public function tieneProductos(int $id): bool
    {
        return Proveedor::where('id', $id)
            ->whereHas('productos')
            ->exists();
    }
}
