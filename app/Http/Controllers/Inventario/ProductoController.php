<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Repositories\Inventario\ProductoRepository;
use App\Services\Inventario\ProductoService;
use Illuminate\Http\Request;
use App\Models\Inventario\Producto;
use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Inventario\ContratoConvenio;
use App\Models\Ambiente;
use App\Models\Inventario\Proveedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Inventario\ProductoRequest;

class ProductoController extends InventarioController
{
    private const THEME_PRODUCT_STATES = 'ESTADOS DE PRODUCTO';

    protected ProductoRepository $repository;
    protected ProductoService $service;

    public function __construct(
        ProductoRepository $repository,
        ProductoService $service
    ) {
        parent::__construct();
        $this->middleware('auth');
        
        $this->repository = $repository;
        $this->service = $service;
        
        // Middlewares de permisos de inventario
        $this->middleware('can:VER PRODUCTO')->only(['index', 'show']);
        $this->middleware('can:VER CATALOGO PRODUCTO')->only(['catalogo']);
        $this->middleware('can:BUSCAR PRODUCTO')->only(['buscar']);
        $this->middleware('can:CREAR PRODUCTO')->only(['create', 'store']);
        $this->middleware('can:EDITAR PRODUCTO')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR PRODUCTO')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filtros = [
            'search' => $request->input('search'),
            'per_page' => 10
        ];

        $productos = $this->repository->obtenerConFiltros($filtros);
        $productos->appends($request->only('search'));

        // Cargar marca y categoria directamente para cada producto
        foreach ($productos as $producto) {
            if ($producto->marca_id) {
                $producto->marca = Parametro::find($producto->marca_id);
            }
            if ($producto->categoria_id) {
                $producto->categoria = Parametro::find($producto->categoria_id);
            }
        }
        
        return view('inventario.productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $opciones = $this->service->obtenerOpcionesFormulario(self::THEME_PRODUCT_STATES);
        
        $contratosConvenios = ContratoConvenio::all();
        $ambientes = Ambiente::all();
        $proveedores = Proveedor::all();

        return view(
            'inventario.productos.create',
            array_merge($opciones, [
                'contratosConvenios' => $contratosConvenios,
                'ambientes' => $ambientes,
                'proveedores' => $proveedores
            ])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductoRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['imagen'] = $request->hasFile('imagen') ? $request->file('imagen') : null;

        $this->service->crear($validated, Auth::id());

        return redirect()
            ->route('inventario.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $producto = $this->repository->encontrarConRelaciones((int) $id);
        
        if (!$producto) {
            abort(404);
        }

        return view('inventario.productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $producto = Producto::with(['tipoProducto', 'unidadMedida', 'estado'])->findOrFail($id);
        $opciones = $this->service->obtenerOpcionesFormulario(self::THEME_PRODUCT_STATES);
        
        $contratosConvenios = ContratoConvenio::all();
        $ambientes = Ambiente::all();
        $proveedores = Proveedor::all();
    
        return view('inventario.productos.edit', array_merge($opciones, [
            'producto' => $producto,
            'contratosConvenios' => $contratosConvenios,
            'ambientes' => $ambientes,
            'proveedores' => $proveedores
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoRequest $request, string $id): RedirectResponse
    {
        $producto = Producto::findOrFail($id);
        $validated = $request->validated();
        
        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen');
        }

        $this->service->actualizar($producto, $validated, Auth::id());

        return redirect()
            ->route('inventario.productos.show', $producto->id)
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $producto = Producto::findOrFail($id);
        $this->service->eliminar($producto);
        
        return redirect()
            ->route('inventario.productos.index')
            ->with('success', 'Producto eliminado correctamente');
    }
    
    public function buscarPorCodigo(string $codigo): JsonResponse
    {
        $producto = $this->repository->buscarPorCodigoBarras($codigo);

        if ($producto) {
            return response()->json($producto);
        }

        return response()->json(null, 404);
    }

    /**
     * Mostrar catálogo de productos estilo ecommerce
     */
    public function catalogo(Request $request): View
    {
        $parametroAgotado = Parametro::find(43);
        $estadoAgotadoId = null;

        if ($parametroAgotado) {
            $estadoAgotadoTema = ParametroTema::where('parametro_id', 43)
                ->whereHas('tema', function ($query) {
                    $query->where('name', self::THEME_PRODUCT_STATES);
                })
                ->first();
            
            if ($estadoAgotadoTema) {
                $estadoAgotadoId = $estadoAgotadoTema->id;
            }
        }

        $filtros = [
            'search' => $request->input('search'),
            'tipo_producto_id' => $request->input('tipo_producto_id'),
            'sort_by' => $request->input('sort_by', 'name'),
            'estado_agotado_id' => $estadoAgotadoId,
            'per_page' => 12
        ];

        $productos = $this->repository->obtenerParaCatalogo($filtros);
        $productos->appends([
            'search' => $filtros['search'],
            'tipo_producto_id' => $filtros['tipo_producto_id'],
            'sort_by' => $filtros['sort_by']
        ]);

        // Cargar marca y categoria directamente para cada producto
        foreach ($productos as $producto) {
            if ($producto->marca_id) {
                $producto->marca = Parametro::find($producto->marca_id);
            }
            if ($producto->categoria_id) {
                $producto->categoria = Parametro::find($producto->categoria_id);
            }
        }

        $tiposProductos = ParametroTema::with(['parametro', 'tema'])
            ->whereHas('tema', function ($query) {
                $query->where('name', 'TIPOS DE PRODUCTO');
            })
            ->where('status', 1)
            ->get()
            ->sortBy(function ($tipo) {
                return mb_strtolower($tipo->parametro->name ?? '');
            })
            ->values();

        return view('inventario.productos.card', compact('productos', 'tiposProductos'));
    }

    /**
     * Buscar productos por término de búsqueda (AJAX)
     */
    public function buscar(Request $request): JsonResponse
    {
        $parametroAgotado = Parametro::find(43);
        $estadoAgotadoId = null;

        if ($parametroAgotado) {
            $estadoAgotadoTema = ParametroTema::where('parametro_id', 43)
                ->whereHas('tema', function ($query) {
                    $query->where('name', self::THEME_PRODUCT_STATES);
                })
                ->first();

            if ($estadoAgotadoTema) {
                $estadoAgotadoId = $estadoAgotadoTema->id;
            }
        }

        $filtros = [
            'search' => $request->input('search'),
            'tipo_producto_id' => $request->input('tipo_producto_id'),
            'estado_agotado_id' => $estadoAgotadoId
        ];

        $productos = $this->repository->buscarParaAjax($filtros);

        foreach ($productos as $producto) {
            if ($producto->marca_id) {
                $producto->marca = Parametro::find($producto->marca_id);
            }
            if ($producto->categoria_id) {
                $producto->categoria = Parametro::find($producto->categoria_id);
            }
            $producto->imagen_url = $producto->imagen ? asset($producto->imagen) : null;
        }

        return response()->json([
            'success' => true,
            'productos' => $productos
        ]);
    }


    /**
     * Agregar producto al carrito (AJAX)
     */
    public function agregarAlCarrito(ProductoRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $producto = Producto::findOrFail($validated['producto_id']);

        if ($producto->cantidad < $validated['cantidad']) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente',
                'stock_disponible' => $producto->cantidad
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->producto,
                'stock' => $producto->cantidad
            ]
        ]);
    }

    /**
     * Obtener detalles del producto para modal 
     */
    public function detalles(string $id): View
    {
        $producto = $this->repository->encontrarConRelaciones((int) $id);
        
        if (!$producto) {
            abort(404);
        }

        // Cargar marca y categoria DIRECTAMENTE desde Parametro sin usar la relación del modelo
        if ($producto->marca_id) {
            $producto->setRelation('marca', Parametro::find($producto->marca_id));
        }
        if ($producto->categoria_id) {
            $producto->setRelation('categoria', Parametro::find($producto->categoria_id));
        }

        return view('inventario.productos._detalles-modal', compact('producto'));
    }

    /**
     * Vista imprimible de la etiqueta con código de barras SENA (JS en cliente)
     */
    public function etiqueta(string $id): View
    {
        $producto = Producto::findOrFail($id);
        return view('inventario.productos.etiqueta', compact('producto'));
    }
}

