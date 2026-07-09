<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario;

use App\Exceptions\ProveedorException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventario\Concerns\HandlesProveedorFormActions;
use App\Http\Requests\Inventario\ProveedorRequest;
use App\Inventario\Interfaces\Repositories\Proveedor\ProveedorRepositoryInterface;
use App\Inventario\Services\Proveedor\ProveedorService;
use App\Models\Inventario\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    use HandlesProveedorFormActions;

    protected ProveedorRepositoryInterface $repository;

    protected ProveedorService $service;

    public function __construct(ProveedorRepositoryInterface $repository, ProveedorService $service)
    {
        $this->middleware('can:VER PROVEEDOR')->only('index', 'show');
        $this->middleware('can:CREAR PROVEEDOR')->only('create', 'store');
        $this->middleware('can:EDITAR PROVEEDOR')->only('edit', 'update');
        $this->middleware('can:ELIMINAR PROVEEDOR')->only('destroy');

        $this->repository = $repository;
        $this->service = $service;
    }

    public function index(Request $request): View
    {
        $filtros = ['search' => $request->input('search'), 'per_page' => 10];
        $proveedores = $this->repository->obtenerConFiltros($filtros);
        $proveedores->appends($request->only('search'));

        return view('inventario.proveedores.index', compact('proveedores'));
    }

    public function show(Proveedor $proveedor): View
    {
        $proveedor = $this->repository->encontrarConRelaciones($proveedor->id);

        return view('inventario.proveedores.show', compact('proveedor'));
    }

    public function store(ProveedorRequest $request): RedirectResponse
    {
        try {
            $this->service->crear($request->validated(), Auth::id());

            return redirect()->route('inventario.proveedores.index')
                ->with('success', 'Proveedor creado exitosamente.');
        } catch (ProveedorException $e) {
            return back()->withInput()->with('error', 'Error al crear el proveedor: '.$e->getMessage());
        }
    }

    public function update(ProveedorRequest $request, Proveedor $proveedor): RedirectResponse
    {
        try {
            $this->service->actualizar($proveedor, $request->validated(), Auth::id());

            return redirect()->route('inventario.proveedores.index')
                ->with('success', 'Proveedor actualizado exitosamente.');
        } catch (ProveedorException $e) {
            return back()->withInput()->with('error', 'Error al actualizar el proveedor: '.$e->getMessage());
        }
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        try {
            $this->service->eliminar($proveedor);

            return redirect()->route('inventario.proveedores.index')
                ->with('success', 'Proveedor eliminado exitosamente.');
        } catch (ProveedorException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
