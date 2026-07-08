<?php

namespace App\Http\Controllers\Concerns\RegistroAsistencia;

use App\Models\AsistenciaAprendiz;
use App\Models\FichaCaracterizacion;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesRegistroAsistenciaReadActions
{
    public function obtenerAsistenciasPorJornada(Request $request)
    {
        try {
            $jornadaId = $request->input('jornada_id');
            $fecha = $request->input('fecha', Carbon::today()->format('Y-m-d'));

            $query = AsistenciaAprendiz::with([
                'aprendiz.persona',
                'aprendiz.fichaCaracterizacion.jornadaFormacion',
                'instructorFichaCaracterizacion.instructor.persona',
            ])->whereDate('created_at', $fecha);

            if ($jornadaId) {
                $query->whereHas('aprendiz.fichaCaracterizacion', function ($q) use ($jornadaId): void {
                    $q->where('jornada_formacion_id', $jornadaId);
                });
            }

            $asistencias = $query->orderBy('created_at', 'desc')->get();

            $asistenciasFormateadas = $asistencias->map(function ($asistencia): array {
                $ficha = $asistencia->aprendiz->fichaCaracterizacion;

                return [
                    'id' => $asistencia->id,
                    'aprendiz' => $asistencia->aprendiz->persona->getNombreCompletoAttribute(),
                    'numero_documento' => $asistencia->aprendiz->persona->numero_documento,
                    'hora_ingreso' => $asistencia->hora_ingreso,
                    'hora_salida' => $asistencia->hora_salida,
                    'ficha' => $ficha->ficha,
                    'jornada' => $ficha->jornadaFormacion->jornada ?? 'No especificada',
                    'jornada_id' => $ficha->jornada_id,
                    'fecha' => $asistencia->created_at->format('Y-m-d'),
                    'estado' => $asistencia->hora_salida ? 'completa' : 'en_curso',
                ];
            });

            $agrupadoPorJornada = $asistenciasFormateadas->groupBy('jornada');

            return response()->json([
                'status' => 'success',
                'fecha' => $fecha,
                'total_asistencias' => $asistencias->count(),
                'asistencias' => $asistenciasFormateadas,
                'por_jornada' => $agrupadoPorJornada,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener asistencias por jornada: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener asistencias',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function obtenerFichasConJornadas()
    {
        try {
            $fichas = FichaCaracterizacion::with(['jornadaFormacion', 'programaFormacion'])
                ->where('status', 1)
                ->get()
                ->map(function ($ficha): array {
                    return [
                        'id' => $ficha->id,
                        'ficha' => $ficha->ficha,
                        'programa' => $ficha->programaFormacion->nombre ?? 'No especificado',
                        'jornada' => $ficha->jornadaFormacion->jornada ?? 'No especificada',
                        'jornada_id' => $ficha->jornada_id,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'fichas' => $fichas,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener fichas con jornadas: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener fichas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
