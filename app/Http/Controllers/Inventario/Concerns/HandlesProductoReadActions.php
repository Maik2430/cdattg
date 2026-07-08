<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario\Concerns;

use App\Exceptions\OrdenException;
use App\Http\Requests\Inventario\ProductoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorPNG;

trait HandlesProductoReadActions
{
    public function index(Request $request): View
    {
        $filtros = [
            'search' => $request->input('search'),
            'categoria_id' => $request->input('categoria_id'),
            'marca_id' => $request->input('marca_id'),
            'estado_producto_id' => $request->input('estado_producto_id'),
            'per_page' => 10,
        ];

        $productos = $this->repository->obtenerConFiltros($filtros);
        $productos->appends($request->only(['search', 'categoria_id', 'marca_id', 'estado_producto_id']));
        $this->enrichmentService->enriquecerConMarcasYCategorias($productos);

        $categorias = $this->formOptionsService->obtenerCategorias();
        $marcas = $this->formOptionsService->obtenerMarcas();
        $estadosProducto = $this->formOptionsService->obtenerEstados(self::THEME_PRODUCT_STATES);

        return view('inventario.productos.index', compact('productos', 'categorias', 'marcas', 'estadosProducto'));
    }

    public function show(string $id): View
    {
        $producto = $this->repository->encontrarConRelaciones((int) $id);
        if (! $producto) {
            abort(404);
        }

        return view('inventario.productos.show', compact('producto'));
    }

    public function buscarPorCodigo(string $codigo): JsonResponse
    {
        $producto = $this->repository->buscarPorCodigoBarras($codigo);

        return $producto ? response()->json($producto) : response()->json(null, 404);
    }

    public function catalogo(Request $request): View
    {
        $estadoAgotado = $this->formOptionsService->obtenerEstadoAgotado(self::THEME_PRODUCT_STATES);

        $filtros = [
            'search' => $request->input('search'),
            'tipo_producto_id' => $request->input('tipo_producto_id'),
            'categoria_id' => $request->input('categoria_id'),
            'sort_by' => $request->input('sort_by', 'random'),
            'estado_agotado_id' => $estadoAgotado?->id,
            'per_page' => 12,
        ];

        $productos = $this->repository->obtenerParaCatalogo($filtros);
        $productos->appends([
            'search' => $filtros['search'],
            'tipo_producto_id' => $filtros['tipo_producto_id'],
            'categoria_id' => $filtros['categoria_id'],
            'sort_by' => $filtros['sort_by'],
        ]);

        $tiposProductos = $this->repository->obtenerTiposProductos();
        $categorias = $this->formOptionsService->obtenerCategorias();

        return view('inventario.productos.card', compact('productos', 'tiposProductos', 'categorias'));
    }

    public function buscar(Request $request): JsonResponse
    {
        $estadoAgotado = $this->formOptionsService->obtenerEstadoAgotado(self::THEME_PRODUCT_STATES);
        $filtros = [
            'search' => $request->input('search'),
            'tipo_producto_id' => $request->input('tipo_producto_id'),
            'estado_agotado_id' => $estadoAgotado?->id,
        ];

        $productos = $this->repository->buscarParaAjax($filtros);
        $this->enrichmentService->enriquecerConMarcasYCategorias($productos);

        foreach ($productos as $producto) {
            $producto->imagen_url = $producto->imagen ? asset($producto->imagen) : null;
        }

        return response()->json(['success' => true, 'productos' => $productos]);
    }

    public function agregarAlCarrito(ProductoRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $producto = $this->repository->encontrar((int) $validated['producto_id']);
        if (! $producto) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
        }

        try {
            $this->stockValidator->validarStockSuficiente($producto, $validated['cantidad']);
        } catch (OrdenException) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente',
                'stock_disponible' => $producto->cantidad,
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->name,
                'stock' => $producto->cantidad,
            ],
        ]);
    }

    public function detalles(string $id): View
    {
        $producto = $this->repository->encontrarConRelaciones((int) $id);
        if (! $producto) {
            abort(404);
        }

        $this->enrichmentService->enriquecerProducto($producto);

        return view('inventario.productos._detalles-modal', compact('producto'));
    }

    public function etiqueta(string $id): View
    {
        $producto = $this->repository->encontrar((int) $id);
        if (! $producto) {
            abort(404);
        }

        $barcodeImage = null;
        if (! empty($producto->codigo_barras)) {
            $generator = new BarcodeGeneratorPNG();
            $binary = $generator->getBarcode((string) $producto->codigo_barras, $generator::TYPE_CODE_128, 2, 60);
            $barcodeImage = 'data:image/png;base64,' . base64_encode($binary);
        }

        return view('inventario.productos.etiqueta', [
            'producto' => $producto,
            'barcodeImage' => $barcodeImage,
        ]);
    }
}
