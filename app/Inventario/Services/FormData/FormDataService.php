<?php

declare(strict_types=1);

namespace App\Inventario\Services\FormData;

use App\Inventario\Interfaces\Repositories\ContratoConvenio\ContratoConvenioRepositoryInterface;
use App\Inventario\Interfaces\Repositories\Proveedor\ProveedorRepositoryInterface;
use App\Models\Ambiente;
use Illuminate\Database\Eloquent\Collection;

/**
 * Servicio para obtener datos de formularios de productos
 * Centraliza la obtención de opciones para crear/editar productos
 */
class FormDataService
{
    public function __construct(
        protected ContratoConvenioRepositoryInterface $contratoConvenioRepository,
        protected ProveedorRepositoryInterface $proveedorRepository
    ) {}

    /**
     * Obtiene todos los datos necesarios para formularios de productos
     *
     * @return array{
     *     contratosConvenios: Collection,
     *     ambientes: Collection,
     *     proveedores: Collection
     * }
     */
    public function obtenerDatosFormulario(): array
    {
        return [
            'contratosConvenios' => $this->contratoConvenioRepository->obtenerTodos(),
            'ambientes' => Ambiente::all(),
            'proveedores' => $this->proveedorRepository->obtenerTodos()
        ];
    }

    /**
     * Obtiene contratos y convenios disponibles
     */
    public function obtenerContratosConvenios(): Collection
    {
        return $this->contratoConvenioRepository->obtenerTodos();
    }

    /**
     * Obtiene ambientes disponibles
     */
    public function obtenerAmbientes(): Collection
    {
        return Ambiente::all();
    }

    /**
     * Obtiene proveedores disponibles
     */
    public function obtenerProveedores(): Collection
    {
        return $this->proveedorRepository->obtenerTodos();
    }
}

