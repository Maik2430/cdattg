<?php

namespace App\Repositories\Concerns\AsistenciaAprendiz;

use App\Models\AsistenciaAprendiz;
use Carbon\Carbon;

trait HandlesAsistenciaAprendizWriteActions
{
    public function crear(array $datos): AsistenciaAprendiz
    {
        if (isset($datos['hora_ingreso'])) {
            $datos['hora_ingreso'] = Carbon::parse($datos['hora_ingreso'])->format('Y-m-d H:i:s');
        }

        return AsistenciaAprendiz::create($datos);
    }

    public function crearLote(array $asistencias, int $caracterizacionId): int
    {
        $registros = [];

        foreach ($asistencias as $asistencia) {
            $registros[] = [
                'caracterizacion_id' => $caracterizacionId,
                'nombres' => $asistencia['nombres'],
                'apellidos' => $asistencia['apellidos'],
                'numero_identificacion' => $asistencia['numero_identificacion'],
                'hora_ingreso' => Carbon::parse($asistencia['hora_ingreso'])->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        AsistenciaAprendiz::insert($registros);

        return count($registros);
    }

    public function actualizarHoraSalida(int $caracterizacionId, string $fecha, string $horaSalida): int
    {
        return AsistenciaAprendiz::where('caracterizacion_id', $caracterizacionId)
            ->whereDate('created_at', $fecha)
            ->whereNull('hora_salida')
            ->update([
                'hora_salida' => Carbon::parse($horaSalida)->format('Y-m-d H:i:s'),
            ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        return AsistenciaAprendiz::where('id', $id)->update($datos);
    }

    public function actualizarNovedadEntrada(int $id, string $novedad, ?string $horaIngreso = null): bool
    {
        $datos = ['novedad_entrada' => $novedad];

        if ($horaIngreso) {
            $datos['hora_ingreso'] = Carbon::parse($horaIngreso)->format('Y-m-d H:i:s');
        }

        return $this->actualizar($id, $datos);
    }

    public function actualizarNovedadSalida(int $id, string $novedad, ?string $horaSalida = null): bool
    {
        $datos = ['novedad_salida' => $novedad];

        if ($horaSalida) {
            $datos['hora_salida'] = Carbon::parse($horaSalida)->format('Y-m-d H:i:s');
        }

        return $this->actualizar($id, $datos);
    }

    public function obtenerEstadisticas(int $fichaId, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $query = AsistenciaAprendiz::whereHas('caracterizacion', function ($q) use ($fichaId): void {
            $q->where('ficha_id', $fichaId);
        });

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        $asistencias = $query->get();

        return [
            'total_registros' => $asistencias->count(),
            'con_hora_salida' => $asistencias->whereNotNull('hora_salida')->count(),
            'sin_hora_salida' => $asistencias->whereNull('hora_salida')->count(),
            'con_novedad_entrada' => $asistencias->whereNotNull('novedad_entrada')->count(),
            'con_novedad_salida' => $asistencias->whereNotNull('novedad_salida')->count(),
            'aprendices_unicos' => $asistencias->unique('numero_identificacion')->count(),
        ];
    }

    public function existeAsistenciaDelDia(string $numeroDocumento, string $fecha): bool
    {
        return AsistenciaAprendiz::where('numero_identificacion', $numeroDocumento)
            ->whereDate('created_at', $fecha)
            ->exists();
    }
}
