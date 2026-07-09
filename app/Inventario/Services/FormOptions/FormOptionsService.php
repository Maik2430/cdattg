<?php

declare(strict_types=1);

namespace App\Inventario\Services\FormOptions;

use App\Inventario\Interfaces\Repositories\ParametroTema\ParametroTemaRepositoryInterface;
use App\Inventario\Interfaces\Services\FormOptionsServiceInterface;
use App\Models\ParametroTema;
use Illuminate\Support\Collection;

class FormOptionsService implements FormOptionsServiceInterface
{
    public function __construct(
        protected ParametroTemaRepositoryInterface $parametroTemaRepository
    ) {}

    /**
     * Obtiene todas las opciones para formularios de productos
     */
    public function obtenerOpcionesProducto(?string $temaEstados = null): array
    {
        $temaEstados = $temaEstados ?? config('inventario.temas.estados_producto', 'ESTADOS DE PRODUCTO');

        return [
            'tiposProductos' => $this->obtenerTiposProducto(),
            'unidadesMedida' => $this->obtenerUnidadesMedida(),
            'estados' => $this->obtenerEstados($temaEstados),
            'categorias' => $this->obtenerCategorias(),
            'marcas' => $this->obtenerMarcas(),
        ];
    }

    /**
     * Obtiene opciones para formularios de órdenes
     */
    public function obtenerOpcionesOrden(): array
    {
        return [
            'tiposOrden' => $this->obtenerTiposOrden(),
            'estadosOrden' => $this->obtenerEstadosOrden(),
        ];
    }

    /**
     * Obtiene tipos de producto
     */
    public function obtenerTiposProducto(): Collection
    {
        return $this->parametroTemaRepository->obtenerPorTema(
            config('inventario.temas.tipos_producto', 'TIPOS DE PRODUCTO')
        );
    }

    /**
     * Obtiene unidades de medida
     */
    public function obtenerUnidadesMedida(): Collection
    {
        return $this->parametroTemaRepository->obtenerPorTema(
            config('inventario.temas.unidades_medida', 'UNIDADES DE MEDIDA')
        );
    }

    /**
     * Obtiene estados
     */
    public function obtenerEstados(string $tema): Collection
    {
        return $this->parametroTemaRepository->obtenerPorTema($tema);
    }

    /**
     * Obtiene categorías
     */
    public function obtenerCategorias(): Collection
    {
        return $this->parametroTemaRepository->obtenerPorTema(
            config('inventario.temas.categorias', 'CATEGORIAS')
        );
    }

    /**
     * Obtiene marcas
     */
    public function obtenerMarcas(): Collection
    {
        return $this->parametroTemaRepository->obtenerPorTema(
            config('inventario.temas.marcas', 'MARCAS')
        );
    }

    /**
     * Obtiene tipos de orden
     */
    public function obtenerTiposOrden(): Collection
    {
        return $this->parametroTemaRepository->obtenerPorTema(
            config('inventario.temas.tipos_orden', 'TIPOS DE ORDEN')
        );
    }

    /**
     * Obtiene estados de orden
     */
    public function obtenerEstadosOrden(): Collection
    {
        return $this->parametroTemaRepository->obtenerPorTema(
            config('inventario.temas.estados_orden', 'ESTADOS DE ORDEN')
        );
    }

    /**
     * Obtiene el estado "AGOTADO" de productos
     *
     * @return ParametroTema|null
     */
    public function obtenerEstadoAgotado(?string $temaEstados = null)
    {
        $temaEstados = $temaEstados ?? config('inventario.temas.estados_producto', 'ESTADOS DE PRODUCTO');

        return $this->obtenerEstadoOrdenPorNombre('AGOTADO', $temaEstados);
    }

    /**
     * Obtiene un estado de orden por nombre
     *
     * @return ParametroTema|null
     */
    public function obtenerEstadoOrdenPorNombre(string $nombreEstado, ?string $temaEstados = null)
    {
        $temaEstados = $temaEstados ?? config('inventario.temas.estados_orden', 'ESTADOS DE ORDEN');

        return $this->parametroTemaRepository->obtenerEstadoPorNombre($nombreEstado, $temaEstados);
    }
}
