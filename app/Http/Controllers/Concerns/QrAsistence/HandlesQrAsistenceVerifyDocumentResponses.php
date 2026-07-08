<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Events\NuevaAsistenciaRegistrada;
use App\Models\AsistenciaAprendiz;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

trait HandlesQrAsistenceVerifyDocumentResponses
{
    private function procesarAsistenciaExistenteQr(
        AsistenciaAprendiz $asistenciaExistente,
        Persona $persona,
        string $horaIngreso
    ): JsonResponse {
        if ($asistenciaExistente->hora_salida === null) {
            $asistenciaExistente->update([
                'hora_salida' => $horaIngreso,
            ]);

            event(new NuevaAsistenciaRegistrada([
                'id' => $asistenciaExistente->id,
                'aprendiz' => $persona->getNombreCompletoAttribute(),
                'estado' => 'salida',
                'timestamp' => now()->toISOString(),
            ]));

            return response()->json([
                'status' => 'exit_registered',
                'message' => 'Asistencia de salida registrada para '.$persona->getNombreCompletoAttribute().'.',
                'hora_ingreso' => Carbon::parse($asistenciaExistente->hora_ingreso)->format('h:i A'),
                'hora_salida' => Carbon::parse($asistenciaExistente->hora_salida)->format('h:i A'),
                'aprendiz_data' => [
                    'numero_documento' => $persona->numero_documento,
                ],
            ], 200);
        }

        return response()->json([
            'status' => 'attendance_complete',
            'message' => 'El aprendiz '.$persona->getNombreCompletoAttribute().' ya completó su asistencia hoy.',
            'hora_ingreso' => Carbon::parse($asistenciaExistente->hora_ingreso)->format('h:i A'),
            'hora_salida' => Carbon::parse($asistenciaExistente->hora_salida)->format('h:i A'),
            'aprendiz_data' => [
                'numero_documento' => $persona->numero_documento,
            ],
        ], 200);
    }

    private function respuestaEntradaRegistrada(Persona $persona, AsistenciaAprendiz $asistencia): JsonResponse
    {
        $responseData = [
            'status' => 'registered',
            'message' => 'Asistencia de entrada registrada para '.$persona->getNombreCompletoAttribute().'.',
            'hora_ingreso' => Carbon::parse($asistencia->hora_ingreso)->format('h:i A'),
            'aprendiz_data' => [
                'numero_documento' => $persona->numero_documento,
                'primer_nombre' => $persona->primer_nombre,
                'segundo_nombre' => $persona->segundo_nombre,
                'primer_apellido' => $persona->primer_apellido,
                'segundo_apellido' => $persona->segundo_apellido,
            ],
        ];

        return response()->json($responseData, 201);
    }
}
