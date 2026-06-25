<?php

namespace App\Http\Controllers\Aitg\Banco;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aitg\Banco\StoreTipoArchivoRequest;
use App\Http\Requests\Aitg\Banco\UpdateTipoArchivoRequest;
use App\Models\Aitg\Banco\TipoArchivo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TipoArchivoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:VER TIPO ARCHIVO AITG')->only(['index']);
        $this->middleware('can:CREAR TIPO ARCHIVO AITG')->only(['create', 'store']);
        $this->middleware('can:EDITAR TIPO ARCHIVO AITG')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR TIPO ARCHIVO AITG')->only(['destroy']);
    }

    public function index(): View
    {
        return view('aitg.tipos-archivo.index', [
            'tipos' => TipoArchivo::orderBy('orden')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('aitg.tipos-archivo.create');
    }

    public function store(StoreTipoArchivoRequest $request): RedirectResponse
    {
        TipoArchivo::create([
            ...$request->validated(),
            'extensiones_permitidas' => $this->parseExtensiones($request->input('extensiones_permitidas')),
            'es_obligatorio' => $request->boolean('es_obligatorio', true),
            'activo' => $request->boolean('activo', true),
            'user_create_id' => Auth::id(),
            'user_update_id' => Auth::id(),
        ]);

        return redirect()->route('aitg.tipos-archivo.index')
            ->with('success', 'Tipo de archivo creado correctamente.');
    }

    public function edit(TipoArchivo $tipoArchivo): View
    {
        return view('aitg.tipos-archivo.edit', ['tipo' => $tipoArchivo]);
    }

    public function update(UpdateTipoArchivoRequest $request, TipoArchivo $tipoArchivo): RedirectResponse
    {
        $tipoArchivo->update([
            ...$request->validated(),
            'extensiones_permitidas' => $this->parseExtensiones($request->input('extensiones_permitidas')),
            'es_obligatorio' => $request->boolean('es_obligatorio'),
            'activo' => $request->boolean('activo'),
            'user_update_id' => Auth::id(),
        ]);

        return redirect()->route('aitg.tipos-archivo.index')
            ->with('success', 'Tipo de archivo actualizado correctamente.');
    }

    public function destroy(TipoArchivo $tipoArchivo): RedirectResponse
    {
        if ($tipoArchivo->documentos()->exists()) {
            return back()->with('error', 'No se puede eliminar: existen documentos asociados.');
        }

        $tipoArchivo->delete();

        return back()->with('success', 'Tipo de archivo eliminado.');
    }

    /** @param  array<int, string>|string|null  $input */
    private function parseExtensiones(array|string|null $input): array
    {
        if (is_array($input)) {
            $raw = implode(',', $input);
        } else {
            $raw = (string) $input;
        }

        $parts = array_filter(array_map(
            fn ($e) => strtolower(ltrim(trim($e), '.')),
            explode(',', $raw)
        ));

        return $parts ?: ['pdf'];
    }
}
