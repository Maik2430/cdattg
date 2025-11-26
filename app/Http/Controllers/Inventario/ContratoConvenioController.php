<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario;

use App\Repositories\Inventario\ContratoConvenioRepository;
use App\Services\Inventario\ContratoConvenioService;
use App\Models\Inventario\ContratoConvenio;
use App\Models\ParametroTema;
use App\Exceptions\ContratoConvenioException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Inventario\ContratoConvenioRequest;
use Illuminate\Support\Facades\Auth;

class ContratoConvenioController extends InventarioController
{
    protected ContratoConvenioRepository $repository;
    protected ContratoConvenioService $service;

    public function __construct(
        ContratoConvenioRepository $repository,
        ContratoConvenioService $service
    ) {
        parent::__construct();
        $this->middleware('can:VER CONTRATO')->only('index', 'show');
        $this->middleware('can:CREAR CONTRATO')->only('create', 'store');
        $this->middleware('can:EDITAR CONTRATO')->only('edit', 'update');
        $this->middleware('can:ELIMINAR CONTRATO')->only('destroy');
        
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index(Request $request): View
    {
        $filtros = [
            'search' => $request->input('search'),
            'per_page' => 10
        ];

        $contratosConvenios = $this->repository->obtenerConFiltros($filtros);
        $contratosConvenios->appends($request->only('search'));

        $estados = ParametroTema::with(['parametro', 'tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS'))
            ->where('parametros_temas.status', 1)
            ->get();

        return view('inventario.contratos_convenios.index', compact('contratosConvenios', 'estados'));
    }

    public function create(): View
    {
        $proveedores = $this->repository->obtenerProveedores();
        return view('inventario.contratos_convenios.create', compact('proveedores'));
    }

    public function show(ContratoConvenio $contratoConvenio): View
    {
        $contratoConvenio = $this->repository->encontrarConRelaciones($contratoConvenio->id);
        
        if (!$contratoConvenio) {
            abort(404);
        }

        return view('inventario.contratos_convenios.show', compact('contratoConvenio'));
    }

    public function edit(ContratoConvenio $contratoConvenio): View
    {
        $proveedores = $this->repository->obtenerProveedores();
        return view('inventario.contratos_convenios.edit', compact('contratoConvenio', 'proveedores'));
    }

    public function store(ContratoConvenioRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->service->crear($validated, Auth::id());

        return redirect()
            ->route('inventario.contratos-convenios.index')
            ->with('success', 'Contrato/Convenio creado exitosamente.');
    }

    public function update(ContratoConvenioRequest $request, ContratoConvenio $contratoConvenio): RedirectResponse
    {
        $validated = $request->validated();
        $this->service->actualizar($contratoConvenio, $validated, Auth::id());

        return redirect()
            ->route('inventario.contratos-convenios.index')
            ->with('success', 'Contrato/Convenio actualizado exitosamente.');
    }

    public function destroy(ContratoConvenio $contratoConvenio): RedirectResponse
    {
        try {
            $this->service->eliminar($contratoConvenio);
            return redirect()
                ->route('inventario.contratos-convenios.index')
                ->with('success', 'Contrato/Convenio eliminado exitosamente.');
        } catch (ContratoConvenioException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
