<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Events\NuevaAsistenciaRegistrada;
use App\Events\QrScanned;
use App\Models\Aprendiz;
use App\Models\Asistencia;
use App\Models\AsistenciaAprendiz;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\Persona;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistenceVerifyDocument
{
    use HandlesQrAsistenceVerifyDocumentResponses;

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

    /** @return array<string, mixed>|JsonResponse */
    private function resolverContextoVerifyDocument(string $numeroDocumento, int $fichaId): array|JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->persona || ! $user->persona->instructor) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo identificar al instructor actual.',
            ], 403);
        }

        $instructorId = $user->persona->instructor->id;

        $instructorFicha = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
            ->where('ficha_id', $fichaId)
            ->first();

        if (! $instructorFicha) {
            return response()->json([
                'status' => 'not_assigned_instructor',
                'message' => 'El instructor no está asignado a esta ficha.',
            ], 403);
        }

        $persona = Persona::where('numero_documento', $numeroDocumento)->first();
        if (! $persona) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'El aprendiz con documento '.$numeroDocumento.' no se encontró en el sistema.',
            ], 404);
        }

        $aprendiz = $persona->aprendiz;
        if (! $aprendiz) {
            return response()->json([
                'status' => 'not_a_learner',
                'message' => 'La persona encontrada no está registrada como aprendiz.',
            ], 404);
        }

        $aprendizCorrecto = Aprendiz::whereHas('persona', function ($query) use ($numeroDocumento): void {
            $query->where('numero_documento', $numeroDocumento);
        })->where('ficha_caracterizacion_id', $fichaId)->first();

        $aprendizFichaId = $aprendizCorrecto ? $aprendizCorrecto->id : $aprendiz->id;

        $asistenciaActiva = Asistencia::deFicha($fichaId)->activa()->first();
        if (! $asistenciaActiva) {
            return response()->json([
                'status' => 'error',
                'message' => 'No hay una sesión de asistencia activa para esta ficha.',
            ], 409);
        }

        if ($asistenciaActiva->is_finished) {
            return response()->json([
                'status' => 'error',
                'message' => 'La sesión de asistencia ya fue finalizada. No se pueden registrar más ingresos o salidas.',
            ], 409);
        }

        return [
            'persona' => $persona,
            'instructor_id' => $instructorId,
            'instructor_ficha_id' => $instructorFicha->id,
            'aprendiz_ficha_id' => $aprendizFichaId,
            'asistencia_activa' => $asistenciaActiva,
        ];
    }

    /** @param array<string, mixed> $contexto */
    private function registrarEntradaQr(array $contexto, string $numeroDocumento, int $fichaId, string $horaIngreso): JsonResponse
    {
        $persona = $contexto['persona'];
        $asistenciaActiva = $contexto['asistencia_activa'];

        $asistencia = AsistenciaAprendiz::create([
            'asistencia_id' => $asistenciaActiva->id,
            'instructor_ficha_id' => $contexto['instructor_ficha_id'],
            'aprendiz_ficha_id' => $contexto['aprendiz_ficha_id'],
            'hora_ingreso' => $horaIngreso,
            'hora_salida' => null,
        ]);

        event(new QrScanned([
            'numero_documento' => $numeroDocumento,
            'aprendiz_nombre' => $persona->getNombreCompletoAttribute(),
            'ficha_id' => $fichaId,
            'hora_ingreso' => $horaIngreso,
            'tipo' => 'entrada',
            'instructor_id' => $contexto['instructor_id'],
        ]));

        event(new NuevaAsistenciaRegistrada([
            'id' => $asistencia->id,
            'aprendiz' => $persona->getNombreCompletoAttribute(),
            'estado' => 'entrada',
            'timestamp' => now()->toISOString(),
        ]));

        return $this->respuestaEntradaRegistrada($persona, $asistencia);
    }
}
