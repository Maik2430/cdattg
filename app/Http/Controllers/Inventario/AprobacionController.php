<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario;

use App\Repositories\Interfaces\Inventario\OrdenRepositoryInterface;
use App\Services\Inventario\AprobacionService;
use App\Exceptions\AprobacionException;
use Illuminate\Http\Request;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Orden;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Inventario\AprobacionesRequest;
use App\Http\Controllers\Controller;

class AprobacionController extends Controller
{
    protected OrdenRepositoryInterface $repository;
    protected AprobacionService $service;

    public function __construct(
        OrdenRepositoryInterface $repository,
        AprobacionService $service
    ) {
        $this->middleware('can:APROBAR ORDEN')->only(['aprobar', 'rechazar', 'pendientes']);
        
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Mostrar órdenes pendientes de aprobación
     */
    public function pendientes(): View
    {
        $estadoEnEspera = $this->service->obtenerEstadoEnEspera();

        if (!$estadoEnEspera) {
            return view('inventario.aprobaciones.pendientes', ['detalles' => collect()]);
        }

        $detalles = $this->repository->obtenerDetallesPendientes($estadoEnEspera->id);

        return view('inventario.aprobaciones.pendientes', compact('detalles'));
    }

    /**
     * Aprobar una solicitud
     */
    public function aprobar(Request $request, int $detalleOrdenId): RedirectResponse
    {
        try {
            $detalleOrden = DetalleOrden::with(['producto', 'orden'])->findOrFail($detalleOrdenId);
            $this->service->aprobarDetalle($detalleOrden);

            $producto = $detalleOrden->producto;

            return redirect()
                ->back()
                ->with(
                    'success',
                    "Solicitud aprobada exitosamente. Stock actualizado para '{$producto->producto}'."
                );

        } catch (AprobacionException $e) {
            return back()
                ->with('error', 'Error al aprobar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar una solicitud
     */
    public function rechazar(AprobacionesRequest $request, int $detalleOrdenId): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $detalleOrden = DetalleOrden::with(['producto', 'orden'])->findOrFail($detalleOrdenId);
            
            $this->service->rechazarDetalle($detalleOrden, $validated['motivo_rechazo']);

            return redirect()
                ->back()
                ->with('success', 'Solicitud rechazada exitosamente.');

        } catch (AprobacionException $e) {
            return back()
                ->with('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Aprobar toda una orden completa
     */
    public function aprobarOrden(Request $request, int $ordenId): RedirectResponse
    {
        try {
            $orden = Orden::with('detalles.producto')->findOrFail($ordenId);
            $this->service->aprobarOrdenCompleta($orden);

            return redirect()
                ->back()
                ->with(
                    'success',
                    "Orden #{$ordenId} aprobada exitosamente. Stock actualizado para todos los productos."
                );

        } catch (AprobacionException $e) {
            return back()
                ->with('error', 'Error al aprobar la orden: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar toda una orden completa
     */
    public function rechazarOrden(AprobacionesRequest $request, int $ordenId): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $orden = Orden::with('detalles.producto')->findOrFail($ordenId);
            
            $this->service->rechazarOrdenCompleta($orden, $validated['motivo_rechazo']);

            return redirect()
                ->back()
                ->with('success', "Orden #{$ordenId} rechazada exitosamente.");

        } catch (AprobacionException $e) {
            return back()
                ->with('error', 'Error al rechazar la orden: ' . $e->getMessage());
        }
    }
}
