<?php

namespace App\Http\Controllers\Aitg\Evaluacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aitg\Evaluacion\GuardarEvaluacionRequest;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\Convocatoria\Convocatoria;
use App\Models\Aitg\Evaluacion\EvaluacionPostulacion;
use App\Services\Aitg\Evaluacion\AitgEvaluacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EvaluacionController extends Controller
{
    public function __construct(
        private readonly AitgEvaluacionService $evaluacionService
    ) {
        $this->middleware('auth');
        $this->middleware('can:VER EVALUACION AITG');
        $this->middleware('can:EVALUAR POSTULACION AITG')->only(['guardar', 'finalizar', 'iniciar']);
    }

    public function index(): View
    {
        return view('aitg.evaluacion.index', [
            'convocatorias' => $this->evaluacionService->convocatoriasEnEvaluacion(),
        ]);
    }

    public function postulaciones(Convocatoria $convocatoria): View
    {
        return view('aitg.evaluacion.postulaciones', [
            'convocatoria' => $convocatoria->load(['competencia', 'regional', 'plan']),
            'postulaciones' => $this->evaluacionService->postulacionesConvocatoria($convocatoria),
        ]);
    }

    public function iniciar(PostulacionPlan $postulacion): RedirectResponse
    {
        abort_unless($postulacion->esConvocatoria(), 404);

        try {
            $evaluacion = $this->evaluacionService->inicializarEvaluacion($postulacion);

            return redirect()->route('aitg.evaluacion.show', $evaluacion);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(EvaluacionPostulacion $evaluacion): View
    {
        $evaluacion = $this->evaluacionService->cargarDatosEvaluacion($evaluacion);

        return view('aitg.evaluacion.show', compact('evaluacion'));
    }

    public function guardar(GuardarEvaluacionRequest $request, EvaluacionPostulacion $evaluacion): RedirectResponse
    {
        try {
            $this->evaluacionService->guardarBorrador($evaluacion, $request->validated(), Auth::user());

            return back()->with('success', 'Evaluación guardada. El puntaje se actualizó automáticamente.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error guardando evaluación AITG', ['exception' => $e]);

            return back()->with('error', 'No fue posible guardar la evaluación.');
        }
    }

    public function finalizar(GuardarEvaluacionRequest $request, EvaluacionPostulacion $evaluacion): RedirectResponse
    {
        try {
            $this->evaluacionService->finalizarEvaluacion($evaluacion, $request->validated(), Auth::user());

            $evaluacion->refresh();

            if ($evaluacion->estado === 'requiere_subsanacion') {
                return redirect()
                    ->route('aitg.evaluacion.postulaciones', $evaluacion->postulacion->convocatoria_id)
                    ->with('success', 'Se solicitó subsanación documental al aspirante.');
            }

            $mensaje = $evaluacion->estado === 'aprobado'
                ? 'Evaluación finalizada. El aspirante pasará al submódulo de Selección.'
                : 'Evaluación finalizada como rechazada.';

            return redirect()
                ->route('aitg.evaluacion.postulaciones', $evaluacion->postulacion->convocatoria_id)
                ->with('success', $mensaje);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error finalizando evaluación AITG', ['exception' => $e]);

            return back()->with('error', 'No fue posible finalizar la evaluación.');
        }
    }
}
