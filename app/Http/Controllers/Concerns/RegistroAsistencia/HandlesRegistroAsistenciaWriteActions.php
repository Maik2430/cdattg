<?php

namespace App\Http\Controllers\Concerns\RegistroAsistencia;

use App\Events\NuevaAsistenciaRegistrada;
use App\Models\AsistenciaAprendiz;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesRegistroAsistenciaWriteActions
{
    public function registrarEntrada(Request $request)
    {
        $request->validate([
            'instructor_ficha_id' => 'required|exists:instructor_fichas_caracterizacion,id',
            'aprendiz_ficha_id' => 'required|exists:aprendiz_fichas_caracterizacion,id',
            'evidencia_id' => 'nullable|exists:evidencias,id',
        ]);

        try {
            DB::beginTransaction();

            $asistenciaExistente = AsistenciaAprendiz::where('aprendiz_ficha_id', $request->aprendiz_ficha_id)
                ->whereNull('hora_salida')
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($asistenciaExistente) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ya existe un registro de entrada sin salida para hoy',
                    'asistencia' => $asistenciaExistente,
                ], 400);
            }

            $asistencia = AsistenciaAprendiz::create([
                'instructor_ficha_id' => $request->instructor_ficha_id,
                'aprendiz_ficha_id' => $request->aprendiz_ficha_id,
                'evidencia_id' => $request->evidencia_id,
                'hora_ingreso' => Carbon::now()->format('H:i:s'),
                'hora_salida' => null,
            ]);

            $asistencia->load([
                'aprendiz.persona',
                'aprendiz.fichaCaracterizacion.jornadaFormacion',
                'instructorFichaCaracterizacion.instructor.persona',
            ]);

            $nombreAprendiz = $asistencia->aprendiz->persona->getNombreCompletoAttribute();
            $ficha = $asistencia->aprendiz->fichaCaracterizacion;
            $jornada = $ficha->jornadaFormacion->jornada ?? 'No especificada';

            event(new NuevaAsistenciaRegistrada([
                'id' => $asistencia->id,
                'aprendiz' => $nombreAprendiz,
                'estado' => 'entrada',
                'timestamp' => now()->toISOString(),
                'jornada' => $jornada,
                'ficha' => $ficha->ficha,
            ]));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Entrada registrada exitosamente',
                'asistencia' => [
                    'id' => $asistencia->id,
                    'aprendiz' => $nombreAprendiz,
                    'hora_ingreso' => $asistencia->hora_ingreso,
                    'jornada' => $jornada,
                    'ficha' => $ficha->ficha,
                    'fecha' => $asistencia->created_at->format('Y-m-d H:i:s'),
                ],
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar entrada: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error al registrar la entrada',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function registrarSalida(Request $request)
    {
        $request->validate([
            'aprendiz_ficha_id' => 'required|exists:aprendiz_fichas_caracterizacion,id',
        ]);

        try {
            DB::beginTransaction();

            $asistencia = AsistenciaAprendiz::where('aprendiz_ficha_id', $request->aprendiz_ficha_id)
                ->whereNull('hora_salida')
                ->whereDate('created_at', Carbon::today())
                ->latest()
                ->first();

            if (! $asistencia) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontró un registro de entrada sin salida para hoy',
                ], 404);
            }

            $asistencia->hora_salida = Carbon::now()->format('H:i:s');
            $asistencia->save();

            $asistencia->load([
                'aprendiz.persona',
                'aprendiz.fichaCaracterizacion.jornadaFormacion',
                'instructorFichaCaracterizacion.instructor.persona',
            ]);

            $nombreAprendiz = $asistencia->aprendiz->persona->getNombreCompletoAttribute();
            $ficha = $asistencia->aprendiz->fichaCaracterizacion;
            $jornada = $ficha->jornadaFormacion->jornada ?? 'No especificada';

            event(new NuevaAsistenciaRegistrada([
                'id' => $asistencia->id,
                'aprendiz' => $nombreAprendiz,
                'estado' => 'salida',
                'timestamp' => now()->toISOString(),
                'jornada' => $jornada,
                'ficha' => $ficha->ficha,
            ]));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Salida registrada exitosamente',
                'asistencia' => [
                    'id' => $asistencia->id,
                    'aprendiz' => $nombreAprendiz,
                    'hora_ingreso' => $asistencia->hora_ingreso,
                    'hora_salida' => $asistencia->hora_salida,
                    'jornada' => $jornada,
                    'ficha' => $ficha->ficha,
                    'fecha' => $asistencia->created_at->format('Y-m-d H:i:s'),
                ],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar salida: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error al registrar la salida',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
