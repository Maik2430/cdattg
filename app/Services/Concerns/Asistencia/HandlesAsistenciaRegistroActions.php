<?php

namespace App\Services\Concerns\Asistencia;

use App\Events\NuevaAsistenciaRegistrada;
use App\Models\AsistenciaAprendiz;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesAsistenciaRegistroActions
{
    public function registrarAsistencia(array $datos): AsistenciaAprendiz
    {
        return DB::transaction(function () use ($datos): AsistenciaAprendiz {
            $asistencia = $this->repository->crear($datos);

            event(new NuevaAsistenciaRegistrada([
                'id' => $asistencia->id,
                'aprendiz' => $asistencia->nombres.' '.$asistencia->apellidos,
                'estado' => 'entrada',
                'timestamp' => now()->toISOString(),
            ]));

            Log::info('Asistencia registrada', [
                'asistencia_id' => $asistencia->id,
                'numero_identificacion' => $asistencia->numero_identificacion,
                'hora_ingreso' => $asistencia->hora_ingreso,
            ]);

            return $asistencia;
        });
    }

    public function registrarAsistenciaLote(array $asistencias, int $caracterizacionId): int
    {
        return DB::transaction(function () use ($asistencias, $caracterizacionId): int {
            $cantidad = $this->repository->crearLote($asistencias, $caracterizacionId);

            Log::info('Asistencias registradas en lote', [
                'cantidad' => $cantidad,
                'caracterizacion_id' => $caracterizacionId,
            ]);

            return $cantidad;
        });
    }

    public function actualizarHoraSalida(int $caracterizacionId, string $fecha, string $horaSalida): int
    {
        return DB::transaction(function () use ($caracterizacionId, $fecha, $horaSalida): int {
            $cantidad = $this->repository->actualizarHoraSalida($caracterizacionId, $fecha, $horaSalida);

            Log::info('Horas de salida actualizadas', [
                'cantidad' => $cantidad,
                'caracterizacion_id' => $caracterizacionId,
                'fecha' => $fecha,
            ]);

            return $cantidad;
        });
    }
}
