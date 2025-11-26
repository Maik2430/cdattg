<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario;

use App\Repositories\Inventario\DevolucionRepository;
use App\Services\Inventario\OrdenService;
use App\Exceptions\DevolucionException;
use App\Exceptions\OrdenException;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Devolucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Inventario\DevolucionRequest;

class DevolucionController extends InventarioController
{
    protected DevolucionRepository $repository;
    protected OrdenService $ordenService;

    public function __construct(
        DevolucionRepository $repository,
        OrdenService $ordenService
    ) {
        parent::__construct();
        $this->middleware('can:DEVOLVER PRESTAMO')->only(['index', 'create', 'store']);
        
        $this->repository = $repository;
        $this->ordenService = $ordenService;
    }

    // Mostrar lista de préstamos pendientes de devolución
    public function index(): View
    {
        $estadoAprobadaId = $this->getEstadoOrdenAprobadaId();
        $prestamos = $this->repository->obtenerPrestamosPendientes($estadoAprobadaId);

        return view('inventario.devoluciones.index', compact('prestamos'));
    }


    // Mostrar formulario de devolución
    public function create(int $detalleOrdenId): View|RedirectResponse
    {
        $detalleOrden = DetalleOrden::with(['orden', 'producto'])->findOrFail($detalleOrdenId);
        
        if ($detalleOrden->estaCompletamenteDevuelto()) {
            return redirect()
                ->route('inventario.devoluciones.index')
                ->with('error', 'Este préstamo ya fue completamente devuelto.');
        }

        return view('inventario.devoluciones.create', compact('detalleOrden'));
    }

    
    // Registrar devolución
    public function store(DevolucionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ((int) $validated['cantidad_devuelta'] === 0) {
            $observaciones = $validated['observaciones'] ?? '';
            if (trim($observaciones) === '') {
                throw ValidationException::withMessages([
                    'observaciones' => 'Debes indicar el motivo cuando registras una devolución de cantidad cero.',
                ]);
            }
        }

        try {
            $devolucion = Devolucion::registrarDevolucion(
                (int) $validated['detalle_orden_id'],
                (int) $validated['cantidad_devuelta'],
                $validated['observaciones'] ?? null
            );

            $mensaje = 'Devolución registrada exitosamente.';
            
            if ($devolucion->cierra_sin_stock) {
                $mensaje .= ' Se registró el consumo total sin restaurar stock.';
            }

            if ($devolucion->getDiasRetrasoDevolucion() > 0) {
                $mensaje .= ' NOTA: La devolución se realizó con ' . $devolucion->getDiasRetrasoDevolucion() . ' días de retraso.';
            }

            return redirect()
                ->route('inventario.devoluciones.index')
                ->with('success', $mensaje);

        } catch (DevolucionException $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al registrar la devolución: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error inesperado al registrar la devolución: ' . $e->getMessage());
        }
    }

    // Mostrar historial de devoluciones
    public function historial(): View
    {
        $devoluciones = $this->repository->obtenerHistorial();

        return view('inventario.devoluciones.historial', compact('devoluciones'));
    }

    // Ver detalle de una devolución
    public function show(int $id): View
    {
        $devolucion = $this->repository->encontrarConRelaciones($id);
        
        if (!$devolucion) {
            abort(404);
        }

        return view('inventario.devoluciones.show', compact('devolucion'));
    }
    // Mostrar préstamos activos del usuario actual
    public function misPrestamos(): View
    {
        $userId = Auth::id();
        $estadoAprobadaId = $this->getEstadoOrdenAprobadaId();
        $prestamos = $this->repository->obtenerPrestamosActivosUsuario($userId, $estadoAprobadaId);

        return view('inventario.prestamos.mis', compact('prestamos'));
    }

    // Historial de préstamos del usuario
    public function historialPrestamos(): View
    {
        $userId = Auth::id();
        $prestamos = $this->repository->obtenerHistorialPrestamosUsuario($userId);

        return view('inventario.prestamos.historial', compact('prestamos'));
    }

    private function getEstadoOrdenAprobadaId(): int
    {
        $estadoAprobada = $this->ordenService->obtenerEstadoAprobada();
        return (int) $estadoAprobada->id;
    }
}
