<?php

namespace App\Http\Controllers\Concerns\AsistenciaAprendices;

use App\Models\AsistenciaAprendiz;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesAsistenciaAprendicesWriteActions
{
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            if (empty($data)) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }

            if (isset($data['attendance'])) {
                $cantidad = $this->asistenciaService->registrarAsistenciaLote(
                    $data['attendance'],
                    $data['caracterizacion_id']
                );

                return response()->json(['message' => "Lista de {$cantidad} asistencias guardada con éxito"], 200);
            }

            $this->asistenciaService->registrarAsistencia($data);

            return response()->json(['message' => 'Asistencia guardada con éxito'], 200);
        } catch (Exception $e) {
            Log::error('Error guardando asistencia: '.$e->getMessage());

            return response()->json(['message' => 'Error guardando asistencia', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = $request->all();

            if (! isset($data['caracterizacion_id']) || ! isset($data['hora_salida']) || ! isset($data['fecha'])) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }

            $cantidad = $this->asistenciaService->actualizarHoraSalida(
                $data['caracterizacion_id'],
                $data['fecha'],
                $data['hora_salida']
            );

            if ($cantidad === 0) {
                return response()->json(['message' => 'No se encontraron asistencias para actualizar'], 404);
            }

            return response()->json(['message' => "Se actualizaron {$cantidad} asistencias con éxito"], 200);
        } catch (Exception $e) {
            Log::error('Error actualizando asistencias: '.$e->getMessage());

            return response()->json(['message' => 'Error actualizando asistencias', 'error' => $e->getMessage()], 500);
        }
    }

    public function assistenceNovedad(Request $request)
    {
        if (! $request->has('caracterizacion_id') || ! $request->has('numero_identificacion') || ! $request->has('hora_entrada') || ! $request->has('novedad')) {
            return response()->json(['message' => 'Datos incompletos'], 400);
        }

        $caracterizacion_id = $request->input('caracterizacion_id');
        $numero_identificacion = $request->input('numero_identificacion');
        $hora_ingreso_peticion = $request->input('hora_entrada');
        $novedad_salida = $request->input('novedad');

        $hora_ingreso = Carbon::parse($hora_ingreso_peticion)->format('H:i:s');

        $asistencia = AsistenciaAprendiz::where('caracterizacion_id', $caracterizacion_id)
            ->where('numero_identificacion', $numero_identificacion)
            ->where('hora_ingreso', $hora_ingreso)
            ->first();

        if (! $asistencia) {
            return response()->json(['message' => 'No se encontró asistencia'], 404);
        }

        $asistencia->hora_salida = Carbon::now();
        $asistencia->novedad_salida = $novedad_salida;
        $asistencia->save();

        return response()->json(['message' => 'Solicitud de respuesta aceptada'], 200);
    }

    public function updateExitAsistence(Request $request)
    {
        try {
            $data = $request->all();

            if (! isset($data['numero_identificacion']) || ! isset($data['hora_ingreso']) || ! isset($data['novedad_salida'])) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }

            $jornada = $this->jornadaValidation->obtenerJornadaPorHora($data['hora_ingreso']);

            if (! $jornada) {
                return response()->json(['message' => 'No se pudo determinar la jornada'], 400);
            }

            $actualizado = $this->asistenciaService->actualizarNovedadSalida(
                $data['caracterizacion_id'] ?? 0,
                $data['numero_identificacion'],
                $data['hora_ingreso'],
                $data['novedad_salida'],
                $jornada
            );

            if ($actualizado) {
                return response()->json(['message' => 'Novedad de salida actualizada'], 200);
            }

            return response()->json(['message' => 'No se pudo actualizar la novedad'], 400);
        } catch (Exception $e) {
            Log::error('Error actualizando novedad de salida: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateEntraceAsistence(Request $request)
    {
        try {
            $data = $request->all();

            if (! isset($data['numero_identificacion']) || ! isset($data['hora_ingreso']) || ! isset($data['novedad_entrada'])) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }

            $jornada = $this->jornadaValidation->obtenerJornadaPorHora($data['hora_ingreso']);

            if (! $jornada) {
                return response()->json(['message' => 'No se pudo determinar la jornada'], 400);
            }

            $actualizado = $this->asistenciaService->actualizarNovedadEntrada(
                $data['caracterizacion_id'] ?? 0,
                $data['numero_identificacion'],
                $data['hora_ingreso'],
                $data['novedad_entrada'],
                $jornada
            );

            if ($actualizado) {
                return response()->json(['message' => 'Novedad de entrada actualizada'], 200);
            }

            return response()->json(['message' => 'No se pudo actualizar la novedad'], 400);
        } catch (Exception $e) {
            Log::error('Error actualizando novedad de entrada: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
