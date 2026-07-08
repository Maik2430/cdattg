<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario\Concerns;

use App\Exceptions\OrdenException;
use App\Http\Requests\Inventario\OrdenRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait HandlesOrdenWriteActions
{
    public function store(OrdenRequest $request): RedirectResponse
    {
        try {
            $this->service->crear($request->validated(), Auth::id());

            return redirect()->route('inventario.ordenes.index')
                ->with('success', 'Orden creada exitosamente. Stock actualizado.');
        } catch (OrdenException $e) {
            return back()->withInput()->with('error', 'Error al crear la orden: ' . $e->getMessage());
        }
    }

    public function storePrestamos(OrdenRequest $request): RedirectResponse
    {
        try {
            $this->service->crearDesdeCarrito($request->validated(), Auth::id());
            Session::forget('carrito_data');

            return redirect()->route('inventario.productos.catalogo')
                ->with('success', 'Solicitud creada exitosamente. Está pendiente de aprobación por el administrador.')
                ->with('clear_cart', true);
        } catch (OrdenException $e) {
            return back()->withInput()->with('error', 'Error al crear la solicitud: ' . $e->getMessage());
        }
    }

    public function update(OrdenRequest $request, string $id): RedirectResponse
    {
        $orden = $this->repository->encontrarConDetallesYDevoluciones((int) $id);
        if (! $orden) {
            abort(404);
        }
        if ($this->service->tieneDevoluciones($orden)) {
            return redirect()->route('inventario.ordenes.index', $orden->id)
                ->with('error', 'No se puede editar una orden que ya tiene devoluciones registradas.');
        }

        try {
            $this->service->actualizar($orden, $request->validated(), Auth::id());

            return redirect()->route('inventario.ordenes.index', $orden->id)
                ->with('success', 'Orden actualizada exitosamente. Stock actualizado.');
        } catch (OrdenException $e) {
            return back()->withInput()->with('error', 'Error al actualizar la orden: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $orden = $this->repository->encontrarConDetallesYDevoluciones((int) $id);
        if (! $orden) {
            abort(404);
        }
        if ($this->service->tieneDevoluciones($orden)) {
            return redirect()->route('inventario.ordenes.index')
                ->with('error', 'No se puede eliminar una orden que ya tiene devoluciones registradas.');
        }

        try {
            $this->service->eliminar($orden);

            return redirect()->route('inventario.ordenes.index')
                ->with('success', 'Orden eliminada exitosamente. Stock restaurado.');
        } catch (OrdenException $e) {
            return redirect()->route('inventario.ordenes.index')
                ->with('error', 'Error al eliminar la orden: ' . $e->getMessage());
        }
    }

    public function vaciarHistorial(): RedirectResponse
    {
        try {
            $resultado = $this->service->vaciarHistorial();
            $mensaje = 'Historial de órdenes vaciado correctamente.';

            if ($resultado['eliminadas'] === 0 && $resultado['pendientes'] > 0) {
                $mensaje = 'No se eliminaron órdenes porque existen préstamos sin devolver.';
            } elseif ($resultado['pendientes'] > 0) {
                $mensaje .= ' No se eliminaron ' . $resultado['pendientes'] . ' órdenes en préstamo sin devolver.';
            }

            return redirect()->route('inventario.ordenes.index')->with('success', $mensaje);
        } catch (OrdenException $e) {
            return redirect()->route('inventario.ordenes.index')
                ->with('error', 'Error al vaciar el historial de órdenes: ' . $e->getMessage());
        }
    }
}
