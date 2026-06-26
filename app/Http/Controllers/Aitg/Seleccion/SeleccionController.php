<?php

namespace App\Http\Controllers\Aitg\Seleccion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aitg\Seleccion\ConfirmarSeleccionRequest;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\Convocatoria\Convocatoria;
use App\Services\Aitg\Seleccion\AitgSeleccionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SeleccionController extends Controller
{
    public function __construct(
        private readonly AitgSeleccionService $seleccionService
    ) {
        $this->middleware('auth');
        $this->middleware('can:VER SELECCION AITG');
        $this->middleware('can:SELECCIONAR INSTRUCTOR AITG')->only(['confirmar']);
    }

    public function index(): View
    {
        return view('aitg.seleccion.index', [
            'convocatorias' => $this->seleccionService->convocatoriasParaSeleccion(),
        ]);
    }

    public function candidatos(Request $request, Convocatoria $convocatoria): View
    {
        $orden = $request->input('orden', 'desc') === 'asc' ? 'asc' : 'desc';

        return view('aitg.seleccion.candidatos', [
            'convocatoria' => $convocatoria->load(['competencia', 'regional', 'plan', 'postulacionSeleccionada.user.persona']),
            'candidatos' => $this->seleccionService->candidatos($convocatoria, $orden),
            'orden' => $orden,
        ]);
    }

    public function confirmar(ConfirmarSeleccionRequest $request, Convocatoria $convocatoria): RedirectResponse
    {
        $ganador = PostulacionPlan::findOrFail($request->validated('postulacion_ganador_id'));
        $suplente = $request->validated('postulacion_suplente_id')
            ? PostulacionPlan::findOrFail($request->validated('postulacion_suplente_id'))
            : null;

        try {
            $this->seleccionService->seleccionarInstructor(
                $convocatoria,
                $ganador,
                Auth::user(),
                $suplente,
                $request->validated('observaciones')
            );

            return redirect()
                ->route('aitg.seleccion.candidatos', $convocatoria)
                ->with('success', 'Instructor seleccionado. La convocatoria fue finalizada y el proceso continúa en Formalización.');
        } catch (\Throwable $e) {
            Log::error('Error en selección AITG', ['exception' => $e]);

            return back()->with('error', $e->getMessage() ?: 'No fue posible confirmar la selección.');
        }
    }
}
