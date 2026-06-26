<?php

namespace App\Http\Controllers\Aitg\Convocatoria;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aitg\Banco\StoreBulkDocumentosBancoRequest;
use App\Http\Requests\Aitg\Banco\StoreDocumentoBancoRequest;
use App\Models\Aitg\Banco\ArchivoTalento;
use App\Models\Aitg\Banco\PostulacionArchivo;
use App\Models\Aitg\Convocatoria\Convocatoria;
use App\Models\Regional;
use App\Services\Aitg\AitgCatalogoService;
use App\Services\Aitg\Banco\AitgBancoTalentoService;
use App\Services\Aitg\Convocatoria\AitgConvocatoriaReglasService;
use App\Services\Aitg\Convocatoria\AitgConvocatoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Convocatorias para aspirantes dentro del módulo AITG. */
class ConvocatoriaPublicaController extends Controller
{
    public function __construct(
        private readonly AitgConvocatoriaService $convocatoriaService,
        private readonly AitgBancoTalentoService $talentoService,
        private readonly AitgConvocatoriaReglasService $reglasService,
        private readonly AitgCatalogoService $catalogoService
    ) {
        $this->middleware('auth');
        $this->middleware('can:SUBIR DOCUMENTO BANCO AITG')->only([
            'postular', 'seleccionarPerfil', 'storeDocumentos', 'storeDocumentosLote', 'reutilizar',
            'enviarPostulacion', 'destroyDocumento', 'destroyPostulacion',
            'formalizacion', 'enviarFormalizacion',
        ]);
    }

    public function index(Request $request): View
    {
        $user = Auth::user();
        $filtros = $request->only(['competencia', 'regional_id', 'estado']);

        $convocatorias = $this->convocatoriaService->listarParaUsuario($user, $filtros);
        $misPostulaciones = $this->talentoService->listarPostulacionesConvocatoria($user);

        $tarjetas = $convocatorias->map(function ($conv) use ($user, $misPostulaciones) {
            return [
                'conv' => $conv,
                'postulacionUsuario' => $misPostulaciones->firstWhere('convocatoria_id', $conv->id),
                'puedePostular' => $user->can('SUBIR DOCUMENTO BANCO AITG')
                    && $this->reglasService->puedePostularUsuario($user, $conv),
                'mensajeBloqueo' => $this->reglasService->mensajeBloqueoPostulacion($user, $conv),
            ];
        });

        return view('aitg.convocatorias.publicas.index', [
            'tarjetas' => $tarjetas,
            'regionales' => Regional::where('status', 1)->orderBy('nombre')->get(),
            'misPostulaciones' => $misPostulaciones,
            'filtros' => $filtros,
            'puedeVerBorrador' => $user->can('VER CONVOCATORIA AITG'),
        ]);
    }

    public function show(Convocatoria $convocatoria): View|RedirectResponse
    {
        abort_unless($convocatoria->esVisiblePara(Auth::user()), 404);

        $convocatoria->load([
            'competencia', 'plan.perfiles', 'plan.checklist', 'plan.puntosAdicionales',
            'regional', 'centroFormacion',
            'postulacionSeleccionada.user.persona', 'postulacionSeleccionada.perfilPlan',
        ]);

        $postulacion = $convocatoria->postulaciones()
            ->where('user_id', Auth::id())
            ->first();

        $puedePostular = Auth::user()->can('SUBIR DOCUMENTO BANCO AITG')
            && $this->reglasService->puedePostularUsuario(Auth::user(), $convocatoria);
        $mensajeBloqueo = $this->reglasService->mensajeBloqueoPostulacion(Auth::user(), $convocatoria);
        $mensajeBancoRecomendado = $this->reglasService->mensajeBancoRecomendado(Auth::user(), $convocatoria);
        $bancoHabilitado = $this->talentoService->bancoHabilitadoParaPlan(Auth::user(), $convocatoria->plan_contratacion_id);

        return view('aitg.convocatorias.publicas.show', compact(
            'convocatoria', 'postulacion', 'puedePostular', 'mensajeBloqueo', 'bancoHabilitado', 'mensajeBancoRecomendado'
        ));
    }

    public function postular(Convocatoria $convocatoria): View|RedirectResponse
    {
        abort_unless($convocatoria->esVisiblePara(Auth::user()), 404);

        $postulacionExistente = $convocatoria->postulaciones()
            ->where('user_id', Auth::id())
            ->first();

        if (! $postulacionExistente && ! $this->reglasService->puedePostularUsuario(Auth::user(), $convocatoria)) {
            $mensaje = $this->reglasService->mensajeBloqueoPostulacion(Auth::user(), $convocatoria)
                ?? 'Esta convocatoria no está abierta para postulaciones.';

            return redirect()->route('aitg.convocatorias.publicas.show', $convocatoria)
                ->with('error', $mensaje);
        }

        if (! $convocatoria->puedePostular() && ! $postulacionExistente?->puedeEditar()) {
            return redirect()->route('aitg.convocatorias.publicas.show', $convocatoria)
                ->with('error', 'Esta convocatoria no está abierta para postulaciones.');
        }

        try {
            $convocatoria->load(['competencia', 'plan.perfiles', 'plan.checklist', 'plan.puntosAdicionales', 'centroFormacion']);
            $plan = $convocatoria->plan;

            $postulacion = $this->talentoService->obtenerPostulacionConvocatoria(Auth::user(), $convocatoria);
            $postulacion->setRelation('plan', $plan);
            $postulacion->setRelation('convocatoria', $convocatoria);

            $secciones = $this->talentoService->seccionesDocumentales($postulacion, Auth::user());
            $puedeEnviar = $this->talentoService->puedeEnviar($postulacion, Auth::user());
            $requiereDocumentosBase = $this->talentoService->requiereDocumentosBaseEnConvocatoria($postulacion, Auth::user());

            return view('aitg.convocatorias.publicas.postulacion', [
                'convocatoria' => $convocatoria,
                'plan' => $plan,
                'postulacion' => $postulacion,
                'persona' => Auth::user()->persona,
                'secciones' => $secciones,
                'puedeEnviar' => $puedeEnviar,
                'requiereDocumentosBase' => $requiereDocumentosBase,
                'bancoHabilitado' => (bool) $this->talentoService->bancoHabilitadoParaPlan(Auth::user(), $convocatoria->plan_contratacion_id),
                'etiquetaBloque' => fn (int $n, int $total) => $this->catalogoService->etiquetaBloque($plan, $n, $total),
            ]);
        } catch (ValidationException $e) {
            return redirect()->route('aitg.convocatorias.publicas.show', $convocatoria)
                ->with('error', collect($e->errors())->flatten()->first());
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('aitg.convocatorias.publicas.show', $convocatoria)
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error al postular en convocatoria AITG', [
                'convocatoria_id' => $convocatoria->id,
                'user_id' => Auth::id(),
                'exception' => $e,
            ]);

            return redirect()->route('aitg.convocatorias.publicas.show', $convocatoria)
                ->with('error', 'No fue posible iniciar la postulación. Si el problema continúa, contacte al administrador.');
        }
    }

    public function destroyPostulacion(Convocatoria $convocatoria): RedirectResponse
    {
        $postulacion = $convocatoria->postulaciones()
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            $this->talentoService->eliminarPostulacion($postulacion, Auth::user());

            return redirect()->route('aitg.convocatorias.publicas.index')
                ->with('success', 'Su postulación fue eliminada. Puede volver a postular si la convocatoria sigue abierta.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage() ?: 'No se pudo eliminar la postulación.');
        }
    }

    public function seleccionarPerfil(Request $request, Convocatoria $convocatoria): RedirectResponse
    {
        $request->validate(['perfil_plan_id' => ['required', 'integer']]);
        $convocatoria->load('plan');
        $postulacion = $this->talentoService->obtenerPostulacionConvocatoria(Auth::user(), $convocatoria);
        $postulacion->setRelation('plan', $convocatoria->plan);

        if (! $postulacion->puedeEditar() || $postulacion->estado !== 'borrador') {
            return back()->with('error', 'No puede modificar el perfil en el estado actual.');
        }

        $this->talentoService->seleccionarPerfil($postulacion, (int) $request->input('perfil_plan_id'), Auth::user());

        return back()->with('success', 'Perfil seleccionado. Solo puede postular a un perfil en el centro de formación de esta convocatoria.');
    }

    public function storeDocumentos(StoreDocumentoBancoRequest $request, Convocatoria $convocatoria): RedirectResponse
    {
        return $this->guardarDocumento($request, $convocatoria);
    }

    public function storeDocumentosLote(StoreBulkDocumentosBancoRequest $request, Convocatoria $convocatoria): RedirectResponse
    {
        $postulacion = $this->talentoService->obtenerPostulacionConvocatoria(Auth::user(), $convocatoria);

        if (! $postulacion->puedeEditar() || $postulacion->requierePerfil()) {
            return back()->with('error', 'Seleccione el perfil y verifique que puede editar la postulación.');
        }

        try {
            $subidos = $this->talentoService->subirArchivosLote($postulacion, $request->file('archivos', []), Auth::user());

            return back()->with('success', $subidos > 0
                ? "Se cargaron {$subidos} documento(s) en su Banco de Talento."
                : 'No se recibieron archivos válidos.');
        } catch (\Throwable $e) {
            Log::error('AITG convocatoria lote', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudieron cargar los documentos.');
        }
    }

    public function destroyDocumento(Convocatoria $convocatoria, PostulacionArchivo $postulacionArchivo): RedirectResponse
    {
        $postulacion = $this->talentoService->obtenerPostulacionConvocatoria(Auth::user(), $convocatoria);

        try {
            $this->talentoService->eliminarDocumentoPostulacion($postulacion, $postulacionArchivo, Auth::user());

            return back()->with('success', 'Documento eliminado. Puede cargar uno nuevo.');
        } catch (\Throwable $e) {
            Log::error('AITG convocatoria eliminar doc', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudo eliminar el documento.');
        }
    }

    public function reutilizar(Request $request, Convocatoria $convocatoria): RedirectResponse
    {
        $request->validate(['archivo_talento_id' => ['required', 'integer', 'exists:aitg_archivos_talento,id']]);
        $postulacion = $this->talentoService->obtenerPostulacionConvocatoria(Auth::user(), $convocatoria);
        $archivo = ArchivoTalento::findOrFail($request->input('archivo_talento_id'));

        try {
            $this->talentoService->reutilizarArchivo($postulacion, $archivo, Auth::user());

            return back()->with('success', 'Documento reutilizado desde su Banco de Talento.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo reutilizar el documento.');
        }
    }

    public function enviarPostulacion(Convocatoria $convocatoria): RedirectResponse
    {
        $postulacion = $this->talentoService->obtenerPostulacionConvocatoria(Auth::user(), $convocatoria);

        if (! $postulacion->puedeEditar()) {
            return back()->with('error', 'La postulación no puede enviarse en este estado.');
        }

        if (! $this->talentoService->puedeEnviar($postulacion, Auth::user())) {
            return back()->with('error', 'Complete el perfil y todos los documentos obligatorios pendientes de corrección.');
        }

        $this->talentoService->enviarRevision($postulacion, Auth::user());

        return redirect()->route('aitg.convocatorias.publicas.show', $convocatoria)
            ->with('success', 'Postulación enviada correctamente a revisión documental.');
    }

    public function formalizacion(Convocatoria $convocatoria): View|RedirectResponse
    {
        abort_unless($convocatoria->esVisiblePara(Auth::user()), 404);

        $postulacion = $convocatoria->postulaciones()
            ->where('user_id', Auth::id())
            ->first();

        if (! $postulacion || ! $postulacion->esEnFormalizacion()) {
            return redirect()->route('aitg.convocatorias.publicas.show', $convocatoria)
                ->with('error', 'Esta convocatoria no requiere formalización documental en su estado actual.');
        }

        $convocatoria->load(['competencia', 'plan.perfiles', 'centroFormacion']);
        $postulacion->load(['perfilPlan', 'plan.competencia']);
        $postulacion->setRelation('convocatoria', $convocatoria);

        $secciones = $this->talentoService->seccionesDocumentales($postulacion, Auth::user());
        $puedeEnviar = $this->talentoService->puedeEnviar($postulacion, Auth::user());

        return view('aitg.convocatorias.publicas.formalizacion', [
            'convocatoria' => $convocatoria,
            'plan' => $convocatoria->plan,
            'postulacion' => $postulacion,
            'persona' => Auth::user()->persona,
            'secciones' => $secciones,
            'puedeEnviar' => $puedeEnviar,
        ]);
    }

    public function enviarFormalizacion(Convocatoria $convocatoria): RedirectResponse
    {
        $postulacion = $convocatoria->postulaciones()
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (! $postulacion->puedeEditar() || ! $postulacion->esEnFormalizacion()) {
            return back()->with('error', 'No puede enviar la formalización en el estado actual.');
        }

        if (! $this->talentoService->puedeEnviar($postulacion, Auth::user())) {
            return back()->with('error', 'Complete todos los documentos obligatorios de formalización.');
        }

        try {
            $this->talentoService->enviarFormalizacion($postulacion, Auth::user());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('aitg.convocatorias.publicas.show', $convocatoria)
            ->with('success', 'Documentos de formalización enviados a revisión.');
    }

    private function guardarDocumento(StoreDocumentoBancoRequest $request, Convocatoria $convocatoria): RedirectResponse
    {
        $postulacion = $this->talentoService->obtenerPostulacionConvocatoria(Auth::user(), $convocatoria);

        if (! $postulacion->puedeEditar()) {
            return back()->with('error', 'No puede modificar documentos en el estado actual.');
        }

        if ($postulacion->requierePerfil() && $postulacion->faseDocumental() === 'inicial') {
            return back()->with('error', 'Seleccione el perfil antes de cargar documentos.');
        }

        try {
            $this->talentoService->subirArchivo(
                $postulacion,
                $request->file('archivo'),
                Auth::user(),
                $request->input('tipo_archivo_id') ? (int) $request->input('tipo_archivo_id') : null,
                $request->input('punto_adicional_id') ? (int) $request->input('punto_adicional_id') : null,
                $request->input('checklist_item_id') ? (int) $request->input('checklist_item_id') : null,
                $request->input('punto_item_id') ? (int) $request->input('punto_item_id') : null,
                $request->input('perfil_plan_id') ? (int) $request->input('perfil_plan_id') : null,
            );

            return back()->with('success', 'Documento cargado correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo cargar el documento.');
        }
    }
}
