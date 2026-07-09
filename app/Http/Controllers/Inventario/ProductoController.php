<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventario\Concerns\HandlesProductoExportActions;
use App\Http\Controllers\Inventario\Concerns\HandlesProductoReadActions;
use App\Http\Controllers\Inventario\Concerns\HandlesProductoWriteActions;
use App\Inventario\Interfaces\Repositories\Producto\ProductoRepositoryInterface;
use App\Inventario\Interfaces\Services\FormOptionsServiceInterface;
use App\Inventario\Interfaces\Services\StockValidatorServiceInterface;
use App\Inventario\Services\FormData\FormDataService;
use App\Inventario\Services\Producto\ProductoService;
use App\Inventario\Services\ProductoEnrichment\ProductoEnrichmentService;
use App\Services\ExportService;

class ProductoController extends Controller
{
    use HandlesProductoExportActions;
    use HandlesProductoReadActions;
    use HandlesProductoWriteActions;

    private const THEME_PRODUCT_STATES = 'ESTADOS DE PRODUCTO';

    protected ProductoRepositoryInterface $repository;

    protected ProductoService $service;

    protected FormOptionsServiceInterface $formOptionsService;

    protected StockValidatorServiceInterface $stockValidator;

    protected ProductoEnrichmentService $enrichmentService;

    protected FormDataService $formDataService;

    protected ExportService $exportService;

    public function __construct(
        ProductoRepositoryInterface $repository,
        ProductoService $service,
        FormOptionsServiceInterface $formOptionsService,
        StockValidatorServiceInterface $stockValidator,
        ProductoEnrichmentService $enrichmentService,
        FormDataService $formDataService,
        ExportService $exportService
    ) {
        $this->middleware('auth');

        $this->repository = $repository;
        $this->service = $service;
        $this->formOptionsService = $formOptionsService;
        $this->stockValidator = $stockValidator;
        $this->enrichmentService = $enrichmentService;
        $this->formDataService = $formDataService;
        $this->exportService = $exportService;

        $this->middleware('can:VER PRODUCTO')->only(['index', 'show']);
        $this->middleware('can:VER CATALOGO PRODUCTO')->only(['catalogo']);
        $this->middleware('can:BUSCAR PRODUCTO')->only(['buscar']);
        $this->middleware('can:CREAR PRODUCTO')->only(['create', 'store']);
        $this->middleware('can:EDITAR PRODUCTO')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR PRODUCTO')->only(['destroy']);
    }
}
