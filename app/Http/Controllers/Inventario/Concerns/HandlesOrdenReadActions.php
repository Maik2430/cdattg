<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario\Concerns;

use App\Exceptions\OrdenException;
use App\Inventario\Services\Aprobacion\AprobacionService;
use App\Models\Inventario\Orden;
use App\Models\ProgramaFormacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

trait HandlesOrdenReadActions
{
    public function index(Request $request): View
    {
        $filtros = [
            'search' => $request->input('search'),
            'per_page' => 15,
        ];

        $user = $request->user();
        if ($user !== null && ! $user->can('VER TODAS LAS ORDENES')) {
            $filtros['user_id'] = $user->id;
        }

        $ordenes = $this->repository->obtenerConFiltros($filtros);
        $ordenes->appends($request->only('search'));

        return view('inventario.ordenes.index', compact('ordenes'));
    }

    public function prestamosSalidas(): View
    {
        $programas = ProgramaFormacion::where('status', true)
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre', 'codigo']);

        return view('inventario.ordenes.prestamos_salidas', compact('programas'));
    }

    public function pendientes(): View
    {
        try {
            $estadoEnEspera = $this->service->obtenerEstadoEnEspera();
            $ordenes = $this->repository->obtenerPendientes((int) $estadoEnEspera->id, $this->currentUserScopeId());
        } catch (OrdenException) {
            $ordenes = collect();
        }

        return view('inventario.ordenes.pendientes', compact('ordenes'));
    }

    public function completadas(): View
    {
        try {
            $estadoAprobada = app(AprobacionService::class)->obtenerEstadoAprobada();
            $ordenes = $this->repository->obtenerCompletadas((int) $estadoAprobada->id, $this->currentUserScopeId());
        } catch (\Exception) {
            $ordenes = collect();
        }

        return view('inventario.ordenes.completadas', compact('ordenes'));
    }

    public function rechazadas(): View
    {
        $estadoRechazada = app(AprobacionService::class)->obtenerEstadoRechazada();
        $ordenes = $this->repository->obtenerRechazadas((int) $estadoRechazada->id, $this->currentUserScopeId());

        return view('inventario.ordenes.rechazadas', compact('ordenes'));
    }

    public function show(Orden $orden): View
    {
        $orden = $this->repository->encontrarConRelaciones($orden->id);
        if (! $orden) {
            abort(404);
        }

        $user = request()->user();
        if ($user !== null && ! $user->can('VER TODAS LAS ORDENES') && (int) $orden->user_create_id !== (int) $user->id) {
            abort(403);
        }

        $backUrl = request()->get('ref') ?? url()->previous() ?? route('inventario.ordenes.index');

        return view('inventario.ordenes.show', compact('orden', 'backUrl'));
    }

    private function currentUserScopeId(): ?int
    {
        $user = Auth::user();
        if ($user !== null && ! $user->can('VER TODAS LAS ORDENES')) {
            return (int) $user->id;
        }

        return null;
    }
}
