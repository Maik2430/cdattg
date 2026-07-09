<?php

namespace App\Providers\Concerns;

trait RegistersInventarioRepositoryBindings
{
    protected function registerInventarioRepositoryBindings(): void
    {
        $this->app->bind(
            \App\Inventario\Interfaces\Repositories\Producto\ProductoRepositoryInterface::class,
            \App\Inventario\Repositories\Producto\ProductoRepository::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Repositories\Categoria\CategoriaRepositoryInterface::class,
            \App\Inventario\Repositories\Categoria\CategoriaRepository::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Repositories\Proveedor\ProveedorRepositoryInterface::class,
            \App\Inventario\Repositories\Proveedor\ProveedorRepository::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Repositories\Orden\OrdenRepositoryInterface::class,
            \App\Inventario\Repositories\Orden\OrdenRepository::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Repositories\Devolucion\DevolucionRepositoryInterface::class,
            \App\Inventario\Repositories\Devolucion\DevolucionRepository::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Repositories\Marca\MarcaRepositoryInterface::class,
            \App\Inventario\Repositories\Marca\MarcaRepository::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Repositories\ContratoConvenio\ContratoConvenioRepositoryInterface::class,
            \App\Inventario\Repositories\ContratoConvenio\ContratoConvenioRepository::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Repositories\Aprobacion\AprobacionRepositoryInterface::class,
            \App\Inventario\Repositories\Aprobacion\AprobacionRepository::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Repositories\Orden\DetalleOrdenRepositoryInterface::class,
            \App\Inventario\Repositories\Orden\DetalleOrdenRepository::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Repositories\ParametroTema\ParametroTemaRepositoryInterface::class,
            \App\Inventario\Repositories\ParametroTema\ParametroTemaRepository::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Repositories\Notification\NotificationRepositoryInterface::class,
            \App\Inventario\Repositories\Notification\NotificationRepository::class
        );
    }
}
