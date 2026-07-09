<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Models\AsistenciaAprendiz;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistenceVerifyDocument
{
    use HandlesQrAsistenceVerifyDocumentContextHelpers;

    public function verifyDocument(Request $request)
    {
        Log::info('=== DEBUG VERIFY DOCUMENT QR ===');
        Log::info('Request data: '.json_encode($request->all()));

        $request->validate([
            'numero_documento' => 'required|string',
            'ficha_id' => 'required|integer|exists:fichas_caracterizacion,id',
            'evidencia_id' => 'required|integer|exists:evidencias,id',
        ]);

        $numeroDocumento = $request->input('numero_documento');
        $fichaId = $request->input('ficha_id');
        $fechaActual = Carbon::now()->format('Y-m-d');
        $horaIngreso = Carbon::now()->format('H:i:s');

        try {
            DB::beginTransaction();

            $contexto = $this->resolverContextoVerifyDocument($numeroDocumento, $fichaId);
            if ($contexto instanceof JsonResponse) {
                DB::rollBack();

                return $contexto;
            }

            $asistenciaExistente = AsistenciaAprendiz::where('asistencia_id', $contexto['asistencia_activa']->id)
                ->where('aprendiz_ficha_id', $contexto['aprendiz_ficha_id'])
                ->where('instructor_ficha_id', $contexto['instructor_ficha_id'])
                ->whereDate('created_at', $fechaActual)
                ->whereNotNull('hora_ingreso')
                ->first();

            if ($asistenciaExistente) {
                $respuesta = $this->procesarAsistenciaExistenteQr(
                    $asistenciaExistente,
                    $contexto['persona'],
                    $horaIngreso
                );
                DB::commit();

                return $respuesta;
            }

            $respuesta = $this->registrarEntradaQr(
                $contexto,
                $numeroDocumento,
                $fichaId,
                $horaIngreso
            );
            DB::commit();

            return $respuesta;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al verificar o registrar asistencia QR: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error en el servidor al procesar la asistencia.',
            ], 500);
        }
    }
}
