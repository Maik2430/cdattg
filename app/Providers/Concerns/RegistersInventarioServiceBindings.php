<?php

namespace App\Providers\Concerns;

trait RegistersInventarioServiceBindings
{
    protected function registerInventarioServiceBindings(): void
    {
        $this->app->bind(
            \App\Inventario\Interfaces\Services\UserRepositoryInterface::class,
            \App\Inventario\Repositories\User\UserRepository::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Services\NotificationServiceInterface::class,
            \App\Inventario\Services\Notification\NotificationService::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Services\ImageServiceInterface::class,
            \App\Inventario\Services\Image\ImageService::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Services\BarcodeServiceInterface::class,
            \App\Inventario\Services\Barcode\BarcodeService::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Services\FormOptionsServiceInterface::class,
            \App\Inventario\Services\FormOptions\FormOptionsService::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Services\StockValidatorServiceInterface::class,
            \App\Inventario\Services\StockValidator\StockValidatorService::class
        );

        $this->app->bind(
            \App\Inventario\Interfaces\Services\TransactionServiceInterface::class,
            \App\Inventario\Services\Transaction\TransactionService::class
        );

        $this->app->singleton(
            \App\Inventario\Services\ProductoEnrichment\ProductoEnrichmentService::class
        );

        $this->app->bind(
            \App\Inventario\Services\Notification\UserNotificationService::class
        );

        $this->app->bind(
            \App\Inventario\Services\Devolucion\DevolucionService::class
        );

        $this->app->bind(
            \App\Inventario\Services\FormData\FormDataService::class
        );

        $this->app->bind(
            \App\Inventario\Services\Producto\ProductoService::class
        );
    }
}
