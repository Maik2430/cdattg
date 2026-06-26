<?php

namespace App\Http\Controllers\Aitg\Banco;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aitg\Banco\ValidarDocumentoBancoRequest;
use App\Http\Requests\Aitg\Banco\ValidarDocumentosLoteBancoRequest;
use App\Models\Aitg\Banco\MotivoRechazo;
use App\Models\Aitg\Banco\PostulacionArchivo;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Services\Aitg\Banco\AitgBancoValidacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ValidacionBancoController extends Controller
{
    public function __construct(
        private readonly AitgBancoValidacionService $validacionService
    ) {
        $this->middleware('auth');
        $this->middleware('can:VER SOLICITUD BANCO AITG');
        $this->middleware('can:VALIDAR DOCUMENTO BANCO AITG')->only(['validar', 'validarLote', 'devolver']);
    }

    public function index(Request $request): View
    {
        $query = PostulacionPlan::with(['user.persona', 'plan.competencia', 'perfilPlan'])
            ->whereIn('estado', ['pendiente_revision', 'requiere_correccion', 'preseleccionado', 'aprobado', 'rechazado'])
            ->orderByDesc('fecha_envio');

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        return view('aitg.validacion-banco.index', [
            'postulaciones' => $query->paginate(15)->appends($request->only('estado')),
        ]);
    }

    public function show(PostulacionPlan $postulacion): View
    {
        $postulacion->load([
            'user.persona',
            'competencia',
            'plan.competencia',
            'plan.perfiles',
            'perfilPlan',
            'archivos.archivoTalento',
            'archivos.tipoArchivo',
            'archivos.puntoAdicional',
            'archivos.validaciones.motivoRechazo',
        ]);

        $motivosRechazo = MotivoRechazo::activos()->get();
        $archivosFase = $this->validacionService->archivosFaseDocumental($postulacion);
        $archivosPendientes = $this->validacionService->archivosPendientesValidacion($postulacion);

        return view('aitg.validacion-banco.show', compact(
            'postulacion',
            'motivosRechazo',
            'archivosFase',
            'archivosPendientes'
        ));
    }

    public function validarLote(ValidarDocumentosLoteBancoRequest $request, PostulacionPlan $postulacion): RedirectResponse
    {
        try {
            $this->validacionService->validarDocumentosLote(
                $postulacion,
                $request->validated('validaciones'),
                Auth::user()
            );

            return back()->with('success', 'Todas las validaciones fueron registradas correctamente.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('AITG validación lote', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudieron registrar las validaciones.');
        }
    }

    public function validar(ValidarDocumentoBancoRequest $request, PostulacionArchivo $archivoPostulacion): RedirectResponse
    {
        try {
            $this->validacionService->validarDocumento($archivoPostulacion, $request->validated(), Auth::user());

            return back()->with('success', $request->input('resultado') === 'aprobado'
                ? 'Documento aprobado.'
                : 'Documento rechazado.');
        } catch (\Throwable $e) {
            Log::error('AITG validación banco', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->with('error', 'No se pudo registrar la validación.');
        }
    }

    public function devolver(Request $request, PostulacionPlan $postulacion): RedirectResponse
    {
        $request->validate([
            'observaciones' => ['required', 'string', 'max:2000'],
        ]);

        $observacion = $request->input('observaciones');
        if ($postulacion->requierePerfil()) {
            $observacion = 'Debe seleccionar el perfil (alternativa) al que aplica. ' . $observacion;
        }

        try {
            $this->validacionService->devolverPostulacion($postulacion, Auth::user(), $observacion);

            return back()->with('success', 'Postulación devuelta al aspirante para corrección.');
        } catch (\Throwable $e) {
            Log::error('AITG devolver postulación', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudo devolver la postulación.');
        }
    }
}
