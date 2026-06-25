<?php

namespace App\Http\Controllers\Aitg\Banco;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aitg\Banco\ValidarDocumentoBancoRequest;
use App\Models\Aitg\Banco\DocumentoBanco;
use App\Models\Aitg\Banco\MotivoRechazo;
use App\Models\Aitg\Banco\SolicitudBanco;
use App\Services\Aitg\Banco\AitgBancoSolicitudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ValidacionBancoController extends Controller
{
    public function __construct(
        private readonly AitgBancoSolicitudService $solicitudService
    ) {
        $this->middleware('auth');
        $this->middleware('can:VER SOLICITUD BANCO AITG');
    }

    public function index(Request $request): View
    {
        $query = SolicitudBanco::with(['user.persona', 'documentos.tipoArchivo'])
            ->whereIn('estado', ['pendiente_revision', 'requiere_correccion', 'aprobado', 'rechazado'])
            ->orderByDesc('fecha_envio');

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        return view('aitg.validacion-banco.index', [
            'solicitudes' => $query->paginate(15)->appends($request->only('estado')),
        ]);
    }

    public function show(SolicitudBanco $solicitud): View
    {
        $solicitud->load([
            'user.persona',
            'documentos.tipoArchivo',
            'documentos.validaciones.motivoRechazo',
            'documentos.validaciones.validador',
        ]);

        $motivosRechazo = MotivoRechazo::activos()->get();

        return view('aitg.validacion-banco.show', compact('solicitud', 'motivosRechazo'));
    }

    public function validar(ValidarDocumentoBancoRequest $request, DocumentoBanco $documento): RedirectResponse
    {
        try {
            $this->solicitudService->validarDocumento(
                $documento,
                Auth::user(),
                $request->input('resultado'),
                $request->input('motivo_rechazo_id'),
                $request->input('descripcion')
            );

            $mensaje = $request->input('resultado') === 'aprobado'
                ? 'Documento aprobado correctamente.'
                : 'Documento rechazado. El aspirante podrá corregirlo.';

            return back()->with('success', $mensaje);
        } catch (\Throwable $e) {
            Log::error('AITG Banco: error al validar documento', ['error' => $e->getMessage()]);

            return back()->with('error', 'No se pudo registrar la validación.');
        }
    }
}
