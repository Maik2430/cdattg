<?php

namespace App\Http\Controllers\Aitg\Banco;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aitg\Banco\StoreBulkDocumentosBancoRequest;
use App\Http\Requests\Aitg\Banco\StoreDocumentoBancoRequest;
use App\Models\Aitg\Banco\ArchivoTalento;
use App\Models\Aitg\Banco\PostulacionArchivo;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\PlanContratacion;
use App\Models\Competencia;
use App\Models\Regional;
use App\Services\Aitg\Banco\AitgBancoTalentoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BancoInstructorController extends Controller
{
    public function __construct(
        private readonly AitgBancoTalentoService $talentoService
    ) {
        $this->middleware('auth');
        $this->middleware('can:VER BANCO INSTRUCTOR AITG');
        $this->middleware('can:SUBIR DOCUMENTO BANCO AITG')->only([
            'store', 'storeLote', 'reutilizar', 'enviarRevision', 'destroyDocumento',
        ]);
    }

    public function index(Request $request): View
    {
        $competencias = $this->talentoService->buscarCompetencias($request->only(['competencia', 'regional_id', 'modalidad']));

        return view('aitg.banco-instructores.buscar', [
            'competencias' => $competencias,
            'regionales' => Regional::where('status', 1)->orderBy('nombre')->get(),
            'modalidades' => PlanContratacion::MODALIDADES,
            'persona' => Auth::user()->persona,
            'misPostulaciones' => $this->talentoService->listarPostulacionesBanco(Auth::user()),
        ]);
    }

    public function postulacion(Competencia $competencia): View|RedirectResponse
    {
        $competencia->load(['aitgPlanes' => fn ($q) => $q->whereIn('estado', ['activo', 'borrador'])->with('regional')]);

        if ($competencia->aitgPlanes->isEmpty()) {
            return redirect()->route('aitg.banco-instructores.index')
                ->with('error', 'Esta competencia no tiene un plan de contratación activo para acreditación.');
        }

        $postulacion = $this->talentoService->obtenerPostulacion(Auth::user(), $competencia);
        $postulacion->setRelation('competencia', $competencia);

        $secciones = $this->talentoService->seccionesDocumentales($postulacion, Auth::user());
        $puedeEnviar = $this->talentoService->puedeEnviar($postulacion, Auth::user());

        return view('aitg.banco-instructores.postulacion', [
            'competencia' => $competencia,
            'postulacion' => $postulacion,
            'persona' => Auth::user()->persona,
            'secciones' => $secciones,
            'puedeEnviar' => $puedeEnviar,
        ]);
    }

    public function store(StoreDocumentoBancoRequest $request, Competencia $competencia): RedirectResponse
    {
        return $this->guardarArchivo($request, $competencia);
    }

    public function storeLote(StoreBulkDocumentosBancoRequest $request, Competencia $competencia): RedirectResponse
    {
        $postulacion = $this->talentoService->obtenerPostulacion(Auth::user(), $competencia);

        if (! $postulacion->puedeEditar()) {
            return back()->with('error', 'No puede modificar documentos en el estado actual.');
        }

        try {
            $subidos = $this->talentoService->subirArchivosLote(
                $postulacion,
                $request->file('archivos', []),
                Auth::user()
            );

            if ($subidos === 0) {
                return back()->with('error', 'No se recibieron archivos válidos para cargar.');
            }

            return back()->with('success', "Se cargaron {$subidos} documento(s) correctamente.");
        } catch (\Throwable $e) {
            Log::error('AITG Banco talento: error lote', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudieron cargar los documentos.');
        }
    }

    public function reutilizar(Request $request, Competencia $competencia): RedirectResponse
    {
        $request->validate(['archivo_talento_id' => ['required', 'integer', 'exists:aitg_archivos_talento,id']]);

        $postulacion = $this->talentoService->obtenerPostulacion(Auth::user(), $competencia);
        $archivo = ArchivoTalento::findOrFail($request->input('archivo_talento_id'));

        try {
            $this->talentoService->reutilizarArchivo($postulacion, $archivo, Auth::user());

            return back()->with('success', 'Se reutilizó un documento existente de su banco de talento.');
        } catch (\Throwable $e) {
            Log::error('AITG Banco talento: error al reutilizar', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudo reutilizar el documento.');
        }
    }

    public function verArchivo(ArchivoTalento $archivo): View|RedirectResponse
    {
        abort_unless($archivo->user_id === Auth::id() || Auth::user()->can('VALIDAR DOCUMENTO BANCO AITG'), 403);

        if (! Storage::disk($archivo->storage_disk)->exists($archivo->storage_path)) {
            return back()->with('error', 'El archivo no está disponible.');
        }

        return view('aitg.banco-instructores.ver-archivo', compact('archivo'));
    }

    public function downloadArchivo(ArchivoTalento $archivo): StreamedResponse|RedirectResponse
    {
        abort_unless($archivo->user_id === Auth::id() || Auth::user()->can('VALIDAR DOCUMENTO BANCO AITG'), 403);

        if (! Storage::disk($archivo->storage_disk)->exists($archivo->storage_path)) {
            return back()->with('error', 'El archivo no está disponible.');
        }

        return Storage::disk($archivo->storage_disk)->download($archivo->storage_path, $archivo->nombre_original);
    }

    public function streamArchivo(ArchivoTalento $archivo): StreamedResponse
    {
        abort_unless($archivo->user_id === Auth::id() || Auth::user()->can('VALIDAR DOCUMENTO BANCO AITG'), 403);

        $disk = Storage::disk($archivo->storage_disk);

        abort_unless($disk->exists($archivo->storage_path), 404);

        return $disk->response($archivo->storage_path, $archivo->nombre_original, [
            'Content-Type' => $archivo->mime_type ?? 'application/pdf',
        ]);
    }

    public function destroyDocumento(Competencia $competencia, PostulacionArchivo $postulacionArchivo): RedirectResponse
    {
        $postulacion = $this->talentoService->obtenerPostulacion(Auth::user(), $competencia);

        try {
            $this->talentoService->eliminarDocumentoPostulacion($postulacion, $postulacionArchivo, Auth::user());

            return back()->with('success', 'Documento eliminado. Puede cargar uno nuevo.');
        } catch (\Throwable $e) {
            Log::error('AITG Banco talento: error al eliminar', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudo eliminar el documento.');
        }
    }

    public function enviarRevision(Competencia $competencia): RedirectResponse
    {
        $postulacion = $this->talentoService->obtenerPostulacion(Auth::user(), $competencia);

        if (! $postulacion->puedeEditar()) {
            return back()->with('error', 'Su postulación ya fue enviada o no puede modificarse.');
        }

        if (! $this->talentoService->puedeEnviar($postulacion, Auth::user())) {
            return back()->with('error', 'Complete todos los documentos obligatorios de postulación (validación inicial).');
        }

        try {
            $this->talentoService->enviarRevision($postulacion, Auth::user());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Documentos enviados a revisión del Banco de Talento.');
    }

    public function destroyPostulacion(Competencia $competencia): RedirectResponse
    {
        $postulacion = PostulacionPlan::where('user_id', Auth::id())
            ->where('competencia_id', $competencia->id)
            ->whereNull('convocatoria_id')
            ->firstOrFail();

        try {
            $this->talentoService->eliminarPostulacion($postulacion, Auth::user());

            return redirect()->route('aitg.banco-instructores.index')
                ->with('success', 'Postulación eliminada. Puede volver a inscribirse en esta competencia cuando lo desee.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage() ?: 'No se pudo eliminar la postulación.');
        }
    }

    private function guardarArchivo(StoreDocumentoBancoRequest $request, Competencia $competencia): RedirectResponse
    {
        $postulacion = $this->talentoService->obtenerPostulacion(Auth::user(), $competencia);

        if (! $postulacion->puedeEditar()) {
            return back()->with('error', 'No puede modificar documentos en el estado actual.');
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
            Log::error('AITG Banco talento: error al subir', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudo cargar el documento.');
        }
    }
}
