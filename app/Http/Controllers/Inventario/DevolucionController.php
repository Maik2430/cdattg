<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventario\Concerns\HandlesDevolucionReadActions;
use App\Http\Controllers\Inventario\Concerns\HandlesDevolucionWriteActions;
use App\Inventario\Interfaces\Repositories\Devolucion\DevolucionRepositoryInterface;
use App\Inventario\Interfaces\Repositories\Orden\DetalleOrdenRepositoryInterface;
use App\Inventario\Services\Devolucion\DevolucionService;

class DevolucionController extends Controller
{
    use HandlesDevolucionReadActions;
    use HandlesDevolucionWriteActions;

    protected DevolucionRepositoryInterface $repository;

    protected DetalleOrdenRepositoryInterface $detalleOrdenRepository;

    protected DevolucionService $service;

    public function __construct(
        DevolucionRepositoryInterface $repository,
        DetalleOrdenRepositoryInterface $detalleOrdenRepository,
        DevolucionService $service
    ) {
        $this->middleware('can:DEVOLVER PRESTAMO')->only(['index', 'create', 'store']);

        $this->repository = $repository;
        $this->detalleOrdenRepository = $detalleOrdenRepository;
        $this->service = $service;
    }
}
