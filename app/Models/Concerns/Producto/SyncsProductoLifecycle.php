<?php

declare(strict_types=1);

namespace App\Models\Concerns\Producto;

trait SyncsProductoLifecycle
{
    protected static function bootSyncsProductoLifecycle(): void
    {
        static::creating(function ($producto) {
            if (isset($producto->name)) {
                $producto->name = strtoupper($producto->name);
            }
        });

        static::updating(function ($producto) {
            if (isset($producto->name)) {
                $producto->name = strtoupper($producto->name);
            }
        });
    }
}
