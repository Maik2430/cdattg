<?php

namespace App\Services\Concerns\Asistencia;

use App\Events\NuevaAsistenciaRegistrada;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

trait HandlesAsistenciaNovedadActions
{
    /**
     * @throws Exception
     */
    public function actualizarNovedadEntrada(
        int $caracterizacionId,
        string $numeroIdentificacion,
        string $horaIngreso,
        string $novedadEntrada,
        string $jornada
    ): bool {
        $fechaActual = Carbon::now()->format('Y-m-d');
        $horaActual = Carbon::now()->format('H:i:s');

        if (! $this->jornadaValidation->validarAsistenciaEnJornada($horaIngreso, $horaActual, $jornada)) {
            throw new Exception('No se puede actualizar la novedad fuera de la jornada correspondiente.');
        }

        $asistencia = $this->repository->buscarAsistencia($caracterizacionId, $numeroIdentificacion, $horaIngreso);

        if (! $asistencia) {
            throw new Exception('Asistencia no encontrada.');
        }

        if ($asistencia->created_at->format('Y-m-d') !== $fechaActual) {
            throw new Exception('Solo se pueden actualizar novedades de asistencias del día actual.');
        }

        $nuevaHoraIngreso = ($jornada === 'Mañana') ? Carbon::now()->format('H:i:s') : null;

        $actualizado = $this->repository->actualizarNovedadEntrada($asistencia->id, $novedadEntrada, $nuevaHoraIngreso);

        Log::info('Novedad de entrada actualizada', [
            'asistencia_id' => $asistencia->id,
            'novedad' => $novedadEntrada,
            'nueva_hora_ingreso' => $nuevaHoraIngreso,
        ]);

        return $actualizado;
    }

    /**
     * @throws Exception
     */
    public function actualizarNovedadSalida(
        int $caracterizacionId,
        string $numeroIdentificacion,
        string $horaIngreso,
        string $novedadSalida,
        string $jornada
    ): bool {
        $fechaActual = Carbon::now()->format('Y-m-d');
        $horaActual = Carbon::now()->format('H:i:s');

        if (! $this->jornadaValidation->validarAsistenciaEnJornada($horaIngreso, $horaActual, $jornada)) {
            throw new Exception('No se puede actualizar la novedad fuera de la jornada correspondiente.');
        }

        $asistencia = $this->repository->buscarAsistencia($caracterizacionId, $numeroIdentificacion, $horaIngreso);

        if (! $asistencia) {
            throw new Exception('Asistencia no encontrada.');
        }

        if ($asistencia->created_at->format('Y-m-d') !== $fechaActual) {
            throw new Exception('Solo se pueden actualizar novedades de asistencias del día actual.');
        }

        $horaSalida = Carbon::now()->format('H:i:s');
        $actualizado = $this->repository->actualizarNovedadSalida($asistencia->id, $novedadSalida, $horaSalida);

        event(new NuevaAsistenciaRegistrada([
            'id' => $asistencia->id,
            'aprendiz' => $asistencia->nombres.' '.$asistencia->apellidos,
            'estado' => 'salida',
            'timestamp' => now()->toISOString(),
        ]));

        Log::info('Novedad de salida actualizada', [
            'asistencia_id' => $asistencia->id,
            'novedad' => $novedadSalida,
            'hora_salida' => $horaSalida,
        ]);

        return $actualizado;
    }
}
