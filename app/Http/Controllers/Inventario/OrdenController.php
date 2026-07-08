<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventario\Concerns\HandlesOrdenReadActions;
use App\Http\Controllers\Inventario\Concerns\HandlesOrdenWriteActions;
use App\Inventario\Interfaces\Repositories\Orden\OrdenRepositoryInterface;
use App\Inventario\Services\Orden\OrdenService;

class OrdenController extends Controller
{
    use HandlesOrdenReadActions;
    use HandlesOrdenWriteActions;

    protected OrdenRepositoryInterface $repository;
    protected OrdenService $service;

    public function __construct(OrdenRepositoryInterface $repository, OrdenService $service)
    {
        $this->middleware('can:VER ORDEN')->only([
            'index',
            'show',
            'prestamosSalidas',
            'pendientes',
            'completadas',
            'rechazadas',
        ]);
        $this->middleware('can:CREAR ORDEN')->only(['store', 'storePrestamos']);
        $this->middleware('can:EDITAR ORDEN')->only(['update']);
        $this->middleware('can:ELIMINAR ORDEN')->only(['destroy', 'vaciarHistorial']);
        $this->middleware('can:APROBAR ORDEN')->only(['aprobar']);
        $this->middleware('can:COMPLETAR ORDEN')->only(['completar']);

        $this->repository = $repository;
        $this->service = $service;
    }
}
