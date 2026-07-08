<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Models\InstructorFichaCaracterizacion;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistenceScheduleActions
{
    public function getProximaClase(Request $request)
    {
        $request->validate([
            'ficha_id' => 'required|integer|exists:fichas_caracterizacion,id',
        ]);

        try {
            $fichaId = $request->input('ficha_id');
            $instructorFicha = $this->resolverInstructorFichaParaFicha($fichaId);

            if ($instructorFicha instanceof JsonResponse) {
                return $instructorFicha;
            }

            $proximaClase = $instructorFicha->obtenerProximaClase();
            $claseActual = $instructorFicha->obtenerClaseActual();

            if (! $proximaClase) {
                return response()->json([
                    'status' => 'no_classes',
                    'message' => 'No hay clases programadas para esta ficha.',
                    'data' => null,
                ], 404);
            }

            $proximaClase['hora_inicio_formatted'] = Carbon::parse($proximaClase['hora_inicio'])->format('h:i A');
            $proximaClase['hora_fin_formatted'] = Carbon::parse($proximaClase['hora_fin'])->format('h:i A');

            return response()->json([
                'status' => 'success',
                'message' => 'Próxima clase obtenida exitosamente.',
                'data' => [
                    'proxima_clase' => $proximaClase,
                    'clase_actual' => $claseActual,
                ],
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener próxima clase: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al obtener la próxima clase.',
            ], 500);
        }
    }

    public function getProximaClaseWeb($fichaId)
    {
        try {
            $instructorFicha = $this->resolverInstructorFichaParaFicha($fichaId);

            if ($instructorFicha instanceof JsonResponse) {
                return $instructorFicha;
            }

            $proximaClase = $instructorFicha->obtenerProximaClase();
            $claseActual = $instructorFicha->obtenerClaseActual();

            return response()->json([
                'status' => 'success',
                'proxima_clase' => $proximaClase,
                'clase_actual' => $claseActual,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener próxima clase web: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al obtener la próxima clase.',
            ], 500);
        }
    }

    private function resolverInstructorFichaParaFicha(int $fichaId): InstructorFichaCaracterizacion|JsonResponse
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
                'status' => 'error',
                'message' => 'El instructor no está asignado a esta ficha.',
            ], 404);
        }

        return $instructorFicha;
    }
}
