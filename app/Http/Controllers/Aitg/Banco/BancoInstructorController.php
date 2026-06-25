<?php

namespace App\Http\Controllers\Aitg\Banco;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aitg\Banco\StoreDocumentoBancoRequest;
use App\Models\Aitg\Banco\DocumentoBanco;
use App\Models\Aitg\Banco\TipoArchivo;
use App\Services\Aitg\Banco\AitgBancoDocumentoService;
use App\Services\Aitg\Banco\AitgBancoSolicitudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BancoInstructorController extends Controller
{
    public function __construct(
        private readonly AitgBancoSolicitudService $solicitudService,
        private readonly AitgBancoDocumentoService $documentoService
    ) {
        $this->middleware('auth');
        $this->middleware('can:VER BANCO INSTRUCTOR AITG');
        $this->middleware('can:SUBIR DOCUMENTO BANCO AITG')->only(['store', 'enviarRevision']);
        $this->middleware('can:ELIMINAR DOCUMENTO BANCO AITG')->only(['destroy']);
    }

    public function index(): View
    {
        $solicitud = $this->solicitudService->obtenerOCrear(Auth::user());
        $solicitud->load(['documentos.tipoArchivo', 'documentos.validaciones.motivoRechazo']);

        $tiposArchivo = TipoArchivo::activos()->get();
        $puedeEnviar = $this->solicitudService->puedeEnviarRevision($solicitud);

        return view('aitg.banco-instructores.index', compact('solicitud', 'tiposArchivo', 'puedeEnviar'));
    }

    public function store(StoreDocumentoBancoRequest $request): RedirectResponse
    {
        $solicitud = $this->solicitudService->obtenerOCrear(Auth::user());

        if (! $solicitud->puedeEditarDocumentos()) {
            return back()->with('error', 'No puede modificar documentos en el estado actual de su solicitud.');
        }

        try {
            $tipo = TipoArchivo::findOrFail($request->input('tipo_archivo_id'));
            $this->documentoService->subir($solicitud, $tipo, $request->file('archivo'), Auth::user());

            return back()->with('success', "Documento «{$tipo->nombre}» cargado correctamente.");
        } catch (\Throwable $e) {
            Log::error('AITG Banco: error al subir documento', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudo cargar el documento.');
        }
    }

    public function destroy(DocumentoBanco $documento): RedirectResponse
    {
        $this->authorize('delete', $documento);

        if (! $documento->solicitud->puedeEditarDocumentos()) {
            return back()->with('error', 'No puede eliminar documentos en el estado actual de su solicitud.');
        }

        try {
            $nombre = $documento->tipoArchivo->nombre ?? 'Documento';
            $this->documentoService->eliminar($documento);

            return back()->with('success', "Documento «{$nombre}» eliminado.");
        } catch (\Throwable $e) {
            Log::error('AITG Banco: error al eliminar documento', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudo eliminar el documento.');
        }
    }

    public function download(DocumentoBanco $documento): StreamedResponse|RedirectResponse
    {
        $this->authorize('view', $documento);

        if (! Storage::disk($documento->storage_disk)->exists($documento->storage_path)) {
            return back()->with('error', 'El archivo no está disponible.');
        }

        return Storage::disk($documento->storage_disk)->download(
            $documento->storage_path,
            $documento->nombre_original
        );
    }

    public function enviarRevision(): RedirectResponse
    {
        $solicitud = $this->solicitudService->obtenerOCrear(Auth::user());

        if (! $solicitud->puedeEditarDocumentos()) {
            return back()->with('error', 'Su solicitud ya fue enviada o no puede modificarse.');
        }

        if (! $this->solicitudService->puedeEnviarRevision($solicitud)) {
            return back()->with('error', 'Debe cargar todos los documentos obligatorios antes de enviar.');
        }

        $this->solicitudService->enviarRevision($solicitud, Auth::user());

        return back()->with('success', 'Solicitud enviada a revisión. Un validador revisará sus documentos.');
    }
}
