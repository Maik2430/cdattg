<?php

namespace App\Http\Controllers\Concerns\ProgramaFormacion;

use App\Models\ProgramaFormacion;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesProgramaFormacionApiActions
{
    public function getByRedConocimiento(string $redConocimientoId): JsonResponse
    {
        try {
            $programas = ProgramaFormacion::where('red_conocimiento_id', $redConocimientoId)
                ->where('status', true)
                ->orderBy('nombre')
                ->get([
                    'id',
                    'codigo',
                    'nombre',
                    'horas_totales',
                    'horas_etapa_lectiva',
                    'horas_etapa_productiva',
                ]);

            return response()->json([
                'success' => true,
                'data' => $programas,
            ]);
        } catch (Exception $e) {
            Log::error('Error al obtener programas por red de conocimiento', [
                'red_conocimiento_id' => $redConocimientoId,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los programas.',
            ], 500);
        }
    }

    public function getByNivelFormacion(string $nivelFormacionId): JsonResponse
    {
        try {
            $programas = ProgramaFormacion::where('nivel_formacion_id', $nivelFormacionId)
                ->where('status', true)
                ->orderBy('nombre')
                ->get([
                    'id',
                    'codigo',
                    'nombre',
                    'horas_totales',
                    'horas_etapa_lectiva',
                    'horas_etapa_productiva',
                ]);

            return response()->json([
                'success' => true,
                'data' => $programas,
            ]);
        } catch (Exception $e) {
            Log::error('Error al obtener programas por nivel de formación', [
                'nivel_formacion_id' => $nivelFormacionId,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los programas.',
            ], 500);
        }
    }

    public function getActivos(): JsonResponse
    {
        try {
            $programas = ProgramaFormacion::where('status', true)
                ->with(['redConocimiento', 'nivelFormacion'])
                ->orderBy('nombre')
                ->get([
                    'id',
                    'codigo',
                    'nombre',
                    'red_conocimiento_id',
                    'nivel_formacion_id',
                    'horas_totales',
                    'horas_etapa_lectiva',
                    'horas_etapa_productiva',
                ]);

            return response()->json([
                'success' => true,
                'data' => $programas,
            ]);
        } catch (Exception $e) {
            Log::error('Error al obtener programas activos', [
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los programas activos.',
            ], 500);
        }
    }
}
