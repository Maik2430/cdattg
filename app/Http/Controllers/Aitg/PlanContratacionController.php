<?php

namespace App\Http\Controllers\Aitg;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aitg\StorePlanContratacionRequest;
use App\Http\Requests\Aitg\UpdatePlanContratacionRequest;
use App\Models\Aitg\PlanContratacion;
use App\Models\Regional;
use App\Services\Aitg\AitgCatalogoService;
use App\Services\Aitg\AitgPlanFormConfigBuilder;
use App\Services\Aitg\AitgPlanSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PlanContratacionController extends Controller
{
    public function __construct(
        private readonly AitgCatalogoService $catalogoService,
        private readonly AitgPlanSyncService $syncService,
        private readonly AitgPlanFormConfigBuilder $formConfigBuilder
    ) {
        $this->middleware('auth');
        $this->middleware('can:VER PLAN CONTRATACION')->only(['index', 'show']);
        $this->middleware('can:CREAR PLAN CONTRATACION')->only(['create', 'store']);
        $this->middleware('can:EDITAR PLAN CONTRATACION')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR PLAN CONTRATACION')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $query = PlanContratacion::with(['regional', 'programaFormacion'])->orderByDesc('created_at');
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('periodo', 'like', "%{$search}%")
                    ->orWhereHas('programaFormacion', fn ($pq) => $pq->where('nombre', 'like', "%{$search}%"));
            });
        }
        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        return view('aitg.planes-contratacion.index', [
            'planes' => $query->paginate(10)->appends($request->only(['search', 'estado'])),
        ]);
    }

    public function create(): View
    {
        return view('aitg.planes-contratacion.create', $this->formData());
    }

    public function store(StorePlanContratacionRequest $request): RedirectResponse
    {
        try {
            $plan = DB::transaction(function () use ($request) {
                $plan = PlanContratacion::create([
                    ...collect($request->validated())->except(['perfiles', 'puntos_adicionales'])->all(),
                    'user_create_id' => Auth::id(),
                    'user_update_id' => Auth::id(),
                ]);
                $this->syncService->syncPerfiles($plan, $request->input('perfiles', []));
                $this->syncService->syncPuntosAdicionales($plan, $request->input('puntos_adicionales', []));

                return $plan;
            });

            return redirect()->route('aitg.planes-contratacion.show', $plan)
                ->with('success', 'Plan de contratación creado correctamente.');
        } catch (\Throwable $e) {
            Log::error('AITG: error al crear plan', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'No se pudo crear el plan de contratación.');
        }
    }

    public function show(PlanContratacion $plan): View
    {
        $plan->load(['regional', 'programaFormacion.nivelFormacion', 'perfiles', 'puntosAdicionales']);

        return view('aitg.planes-contratacion.show', [
            'plan' => $plan,
            'totalPerfiles' => $plan->perfiles->count(),
        ]);
    }

    public function edit(PlanContratacion $plan): View
    {
        $plan->load(['perfiles', 'puntosAdicionales']);

        return view('aitg.planes-contratacion.edit', array_merge($this->formData($plan), ['plan' => $plan]));
    }

    public function update(UpdatePlanContratacionRequest $request, PlanContratacion $plan): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $plan) {
                $plan->update([
                    ...collect($request->validated())->except(['perfiles', 'puntos_adicionales'])->all(),
                    'user_update_id' => Auth::id(),
                ]);
                $this->syncService->syncPerfiles($plan, $request->input('perfiles', []));
                $this->syncService->syncPuntosAdicionales($plan, $request->input('puntos_adicionales', []));
            });

            return redirect()->route('aitg.planes-contratacion.show', $plan)
                ->with('success', 'Plan de contratación actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('AITG: error al actualizar plan', ['id' => $plan->id, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'No se pudo actualizar el plan de contratación.');
        }
    }

    public function destroy(PlanContratacion $plan): RedirectResponse
    {
        try {
            DB::transaction(fn () => $plan->delete());

            return redirect()->route('aitg.planes-contratacion.index')
                ->with('success', 'Plan de contratación eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('AITG: error al eliminar plan', ['id' => $plan->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'No se pudo eliminar el plan de contratación.');
        }
    }

    private function formData(?PlanContratacion $plan = null): array
    {
        return [
            'regionales' => Regional::where('status', 1)->orderBy('nombre')->get(),
            'nivelesFormacion' => $this->catalogoService->nivelesFormacion(),
            'programas' => $this->catalogoService->programasActivos(),
            'aitgFormConfig' => $this->formConfigBuilder->build($plan),
        ];
    }
}
