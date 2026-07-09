<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

trait HandlesDevolucionReadActions
{
    use HandlesDevolucionHelpers;

    // Mostrar lista de préstamos pendientes de devolución
    public function index(): View
    {
        try {
            $estadoAprobadaId = $this->getEstadoOrdenAprobadaId();
            $userId = $this->resolveDevolucionUserIdFilter();

            $prestamos = $this->repository->obtenerPrestamosPendientes($estadoAprobadaId, $userId);

            return view('inventario.devoluciones.index', compact('prestamos'));
        } catch (\Exception $e) {
            Log::error('Error en DevolucionController@index: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    // Mostrar formulario de devolución
    public function create(int $detalleOrdenId): View|RedirectResponse
    {
        try {
            $detalleOrden = $this->detalleOrdenRepository->encontrarConRelaciones($detalleOrdenId);

            if (! $detalleOrden) {
                abort(404);
            }

            $user = Auth::user();

            if ($user !== null
                && ! $user->can('VER TODAS LAS ORDENES')
                && (int) $detalleOrden->orden->user_create_id !== (int) $user->id
            ) {
                abort(403);
            }

            if ($detalleOrden->estaCompletamenteDevuelto()) {
                return redirect()
                    ->route('inventario.devoluciones.index')
                    ->with('error', 'Este préstamo ya fue completamente devuelto.');
            }

            return view('inventario.devoluciones.create', compact('detalleOrden'));
        } catch (\Exception $e) {
            Log::error('Error en DevolucionController@create: '.$e->getMessage(), [
                'exception' => $e,
                'detalle_orden_id' => $detalleOrdenId,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    // Mostrar historial de devoluciones
    public function historial(): View
    {
        $userId = $this->resolveDevolucionUserIdFilter();
        $devoluciones = $this->repository->obtenerHistorial($userId);

        return view('inventario.devoluciones.historial', compact('devoluciones'));
    }

    // Ver detalle de una devolución
    public function show(int $id): View
    {
        $devolucion = $this->repository->encontrarConRelaciones($id);

        if (! $devolucion) {
            abort(404);
        }

        $user = Auth::user();

        if ($user !== null
            && ! $user->can('VER TODAS LAS ORDENES')
            && (int) $devolucion->detalleOrden->orden->user_create_id !== (int) $user->id
        ) {
            abort(403);
        }

        return view('inventario.devoluciones.show', compact('devolucion'));
    }

    // Mostrar préstamos activos del usuario actual
    public function misPrestamos(): View
    {
        $userId = Auth::id();
        $estadoAprobadaId = $this->getEstadoOrdenAprobadaId();
        $prestamos = $this->repository->obtenerPrestamosActivosUsuario($userId, $estadoAprobadaId);

        return view('inventario.prestamos.usuariosPrestamos', compact('prestamos'));
    }

    // Historial de préstamos del usuario
    public function historialPrestamos(): View
    {
        $userId = Auth::id();
        $prestamos = $this->repository->obtenerHistorialPrestamosUsuario($userId);

        return view('inventario.prestamos.historial', compact('prestamos'));
    }
}
