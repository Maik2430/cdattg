<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\ContratoConvenio;

use App\Models\Inventario\ContratoConvenio;
use App\Inventario\Interfaces\Repositories\ContratoConvenio\ContratoConvenioRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ContratoConvenioRepository implements ContratoConvenioRepositoryInterface
{

    /**
     * Obtiene todos los contratos y convenios
     */
    public function obtenerTodos(): Collection
    {
        return ContratoConvenio::orderBy('name')->get();
    }

    /**
     * Obtiene contratos con filtros y relaciones
     */
    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $query = ContratoConvenio::with([
            'proveedor',
            'estado.parametro',
            'userCreate.persona',
            'userUpdate.persona'
        ])->latest();

        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('codigo', 'LIKE', "%{$search}%")
                    ->orWhereHas('proveedor', function ($proveedorQuery) use ($search): void {
                        $proveedorQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $perPage = $filtros['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

    /**
     * Encuentra un contrato por ID con relaciones
     */
    public function encontrarConRelaciones(int $id): ?ContratoConvenio
    {
        return ContratoConvenio::with([
            'proveedor',
            'productos',
            'estado.parametro',
            'userCreate.persona',
            'userUpdate.persona'
        ])->find($id);
    }

    /**
     * Crea un nuevo contrato
     */
    public function crear(array $datos): ContratoConvenio
    {
        return ContratoConvenio::create($datos);
    }

    /**
     * Actualiza un contrato
     */
    public function actualizar(int $id, array $datos): bool
    {
        return ContratoConvenio::where('id', $id)->update($datos) > 0;
    }

    /**
     * Elimina un contrato
     */
    public function eliminar(int $id): bool
    {
        return ContratoConvenio::destroy($id) > 0;
    }

    /**
     * Verifica si un contrato tiene productos asociados
     */
    public function tieneProductos(int $id): bool
    {
        return ContratoConvenio::where('id', $id)
            ->whereHas('productos')
            ->exists();
    }
}

