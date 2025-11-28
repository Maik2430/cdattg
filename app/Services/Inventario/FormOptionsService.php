<?php

declare(strict_types=1);

namespace App\Services\Inventario;

use App\Services\Inventario\Interfaces\FormOptionsServiceInterface;
use App\Models\Tema;

/**
 * Servicio para obtener opciones de formularios de inventario
 * Uso directo de modelos externos (sin SOLID, sin caché)
 */
class FormOptionsService implements FormOptionsServiceInterface
{
    /**
     * Obtiene todas las opciones para formularios de productos
     *
     * @param string|null $temaEstados
     * @return array
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
     *
     * @return array
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
     * Uso directo del modelo Tema/Parametro (clase externa, sin SOLID)
     *
     * @return \Illuminate\Support\Collection
     */
    public function obtenerTiposProducto()
    {
        return $this->obtenerParametrosPorTema(
            config('inventario.temas.tipos_producto', 'TIPOS DE PRODUCTO')
        );
    }

    /**
     * Obtiene unidades de medida
     * Uso directo del modelo Tema/Parametro (clase externa, sin SOLID)
     *
     * @return \Illuminate\Support\Collection
     */
    public function obtenerUnidadesMedida()
    {
        return $this->obtenerParametrosPorTema(
            config('inventario.temas.unidades_medida', 'UNIDADES DE MEDIDA')
        );
    }

    /**
     * Obtiene estados
     * Uso directo del modelo Tema/Parametro (clase externa, sin SOLID)
     *
     * @param string $tema
     * @return \Illuminate\Support\Collection
     */
    public function obtenerEstados(string $tema)
    {
        return $this->obtenerParametrosPorTema($tema);
    }

    /**
     * Obtiene categorías
     * Uso directo del modelo Tema/Parametro (clase externa, sin SOLID)
     *
     * @return \Illuminate\Support\Collection
     */
    public function obtenerCategorias()
    {
        return $this->obtenerParametrosPorTema(
            config('inventario.temas.categorias', 'CATEGORIAS')
        );
    }

    /**
     * Obtiene marcas
     * Uso directo del modelo Tema/Parametro (clase externa, sin SOLID)
     *
     * @return \Illuminate\Support\Collection
     */
    public function obtenerMarcas()
    {
        return $this->obtenerParametrosPorTema(
            config('inventario.temas.marcas', 'MARCAS')
        );
    }

    /**
     * Obtiene tipos de orden
     * Uso directo del modelo Tema/Parametro (clase externa, sin SOLID)
     *
     * @return \Illuminate\Support\Collection
     */
    public function obtenerTiposOrden()
    {
        return $this->obtenerParametrosPorTema(
            config('inventario.temas.tipos_orden', 'TIPOS DE ORDEN')
        );
    }

    /**
     * Obtiene estados de orden
     * Uso directo del modelo Tema/Parametro (clase externa, sin SOLID)
     *
     * @return \Illuminate\Support\Collection
     */
    public function obtenerEstadosOrden()
    {
        return $this->obtenerParametrosPorTema(
            config('inventario.temas.estados_orden', 'ESTADOS DE ORDEN')
        );
    }

    /**
     * Obtiene parámetros por tema (método helper)
     * Uso directo del modelo Tema/Parametro (clase externa, sin SOLID)
     *
     * @param string $nombreTema
     * @return \Illuminate\Support\Collection
     */
    private function obtenerParametrosPorTema(string $nombreTema)
    {
        $tema = Tema::where('name', $nombreTema)->first();
        
        if (!$tema) {
            return collect([]);
        }

        return $tema->parametros()
            ->wherePivot('status', 1)
            ->get()
            ->map(function ($parametro) {
                $objeto = new \stdClass();
                $objeto->id = $parametro->id;
                $objeto->name = $parametro->name;
                $objeto->status = $parametro->status;
                // Crear objeto parametro con los datos necesarios para evitar problemas de serialización
                $objetoParametro = new \stdClass();
                $objetoParametro->id = $parametro->id;
                $objetoParametro->name = $parametro->name;
                $objetoParametro->status = $parametro->status;
                $objeto->parametro = $objetoParametro;
                return $objeto;
            });
    }

}
