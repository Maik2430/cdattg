<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Concerns\Producto;

use App\Models\Inventario\Producto;

trait HandlesProductoRepositoryCrudActions
{
    public function buscarPorCodigoBarras(string $codigo): ?Producto
    {
        return Producto::where('codigo_barras', $codigo)->first();
    }

    public function encontrar(int $id): ?Producto
    {
        return Producto::find($id);
    }

    public function crear(array $datos): Producto
    {
        return Producto::create($datos);
    }

    public function actualizar(Producto $producto, array $datos): bool
    {
        return $producto->update($datos);
    }

    public function eliminar(Producto $producto): bool
    {
        return $producto->delete();
    }

    public function actualizarStock(Producto $producto, int $cantidad): bool
    {
        $producto->cantidad = $cantidad;

        return $producto->save();
    }

    public function obtenerMaxCodigoBarras(): ?string
    {
        return Producto::whereNotNull('codigo_barras')
            ->max('codigo_barras');
    }

    public function existeCodigoBarras(string $codigo): bool
    {
        return Producto::where('codigo_barras', $codigo)->exists();
    }
}
