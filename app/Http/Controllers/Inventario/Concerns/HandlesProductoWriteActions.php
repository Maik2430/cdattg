<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario\Concerns;

use App\Http\Requests\Inventario\ProductoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

trait HandlesProductoWriteActions
{
    public function create(): View
    {
        $opciones = $this->formOptionsService->obtenerOpcionesProducto(self::THEME_PRODUCT_STATES);
        $datosFormulario = $this->formDataService->obtenerDatosFormulario();
        $productos = $this->repository->obtenerParaCatalogo(['per_page' => 12]);
        $tiposProductos = $this->repository->obtenerTiposProductos();

        $this->enrichmentService->enriquecerConMarcasYCategorias($productos);

        return view('inventario.productos.create', array_merge($opciones, $datosFormulario, [
            'productos' => $productos,
            'tiposProductos' => $tiposProductos,
        ]));
    }

    public function store(ProductoRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated['imagen'] = $request->hasFile('imagen') ? $request->file('imagen') : null;
            $this->service->crear($validated, Auth::id());

            return redirect()->route('inventario.productos.index')->with('success', 'Producto creado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear el producto: '.$e->getMessage());
        }
    }

    public function edit(string $id): View
    {
        $producto = $this->repository->encontrarConRelaciones((int) $id);
        if (! $producto) {
            abort(404);
        }

        $opciones = $this->formOptionsService->obtenerOpcionesProducto(self::THEME_PRODUCT_STATES);
        $datosFormulario = $this->formDataService->obtenerDatosFormulario();
        $tiposProductos = $this->repository->obtenerTiposProductos();

        return view('inventario.productos.edit', array_merge($opciones, $datosFormulario, [
            'producto' => $producto,
            'tiposProductos' => $tiposProductos,
        ]));
    }

    public function update(ProductoRequest $request, string $id): RedirectResponse
    {
        try {
            $producto = $this->repository->encontrar((int) $id);
            if (! $producto) {
                abort(404);
            }

            $validated = $request->validated();
            if ($request->hasFile('imagen')) {
                $validated['imagen'] = $request->file('imagen');
            }

            $this->service->actualizar($producto, $validated, Auth::id());

            return redirect()->route('inventario.productos.show', $producto->id)
                ->with('success', 'Producto actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar el producto: '.$e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $producto = $this->repository->encontrar((int) $id);
            if (! $producto) {
                abort(404);
            }

            $this->service->eliminar($producto);

            return redirect()->route('inventario.productos.index')->with('success', 'Producto eliminado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el producto: '.$e->getMessage());
        }
    }
}
