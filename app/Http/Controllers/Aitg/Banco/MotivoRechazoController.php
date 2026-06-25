<?php

namespace App\Http\Controllers\Aitg\Banco;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aitg\Banco\StoreMotivoRechazoRequest;
use App\Http\Requests\Aitg\Banco\UpdateMotivoRechazoRequest;
use App\Models\Aitg\Banco\MotivoRechazo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MotivoRechazoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:VER MOTIVO RECHAZO AITG')->only(['index']);
        $this->middleware('can:CREAR MOTIVO RECHAZO AITG')->only(['create', 'store']);
        $this->middleware('can:EDITAR MOTIVO RECHAZO AITG')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR MOTIVO RECHAZO AITG')->only(['destroy']);
    }

    public function index(): View
    {
        return view('aitg.motivos-rechazo.index', [
            'motivos' => MotivoRechazo::orderBy('orden')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('aitg.motivos-rechazo.create');
    }

    public function store(StoreMotivoRechazoRequest $request): RedirectResponse
    {
        MotivoRechazo::create([
            ...$request->validated(),
            'activo' => $request->boolean('activo', true),
            'user_create_id' => Auth::id(),
            'user_update_id' => Auth::id(),
        ]);

        return redirect()->route('aitg.motivos-rechazo.index')
            ->with('success', 'Motivo de rechazo creado correctamente.');
    }

    public function edit(MotivoRechazo $motivoRechazo): View
    {
        return view('aitg.motivos-rechazo.edit', ['motivo' => $motivoRechazo]);
    }

    public function update(UpdateMotivoRechazoRequest $request, MotivoRechazo $motivoRechazo): RedirectResponse
    {
        $motivoRechazo->update([
            ...$request->validated(),
            'activo' => $request->boolean('activo'),
            'user_update_id' => Auth::id(),
        ]);

        return redirect()->route('aitg.motivos-rechazo.index')
            ->with('success', 'Motivo de rechazo actualizado correctamente.');
    }

    public function destroy(MotivoRechazo $motivoRechazo): RedirectResponse
    {
        if ($motivoRechazo->validaciones()->exists()) {
            return back()->with('error', 'No se puede eliminar: existen validaciones asociadas.');
        }

        $motivoRechazo->delete();

        return back()->with('success', 'Motivo de rechazo eliminado.');
    }
}
