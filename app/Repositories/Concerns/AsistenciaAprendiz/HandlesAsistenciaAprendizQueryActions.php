<?php

namespace App\Repositories\Concerns\AsistenciaAprendiz;

use App\Models\AsistenciaAprendiz;
use Illuminate\Database\Eloquent\Collection;

trait HandlesAsistenciaAprendizQueryActions
{
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return AsistenciaAprendiz::whereHas('caracterizacion', function ($query) use ($fichaId): void {
            $query->where('ficha_id', $fichaId);
        })
            ->with(['caracterizacion.ficha', 'caracterizacion.jornada'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function obtenerPorFichaYFechas(int $fichaId, string $fechaInicio, string $fechaFin): Collection
    {
        return AsistenciaAprendiz::whereHas('caracterizacion', function ($query) use ($fichaId): void {
            $query->where('ficha_id', $fichaId);
        })
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with(['caracterizacion.ficha', 'caracterizacion.jornada'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function obtenerPorDocumento(string $numeroDocumento): Collection
    {
        return AsistenciaAprendiz::where('numero_identificacion', $numeroDocumento)
            ->with(['caracterizacion.ficha', 'caracterizacion.jornada'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function obtenerDocumentosPorFicha(int $fichaId): Collection
    {
        return AsistenciaAprendiz::select('numero_identificacion')
            ->whereHas('caracterizacion', function ($query) use ($fichaId): void {
                $query->where('ficha_id', $fichaId);
            })
            ->distinct()
            ->get();
    }

    public function obtenerPorFichaJornadaYFecha(string $ficha, string $jornada, string $fecha): Collection
    {
        return AsistenciaAprendiz::whereHas('caracterizacion', function ($query) use ($ficha, $jornada): void {
            $query->whereHas('ficha', function ($query) use ($ficha): void {
                $query->where('ficha', $ficha);
            })->whereHas('jornada', function ($query) use ($jornada): void {
                $query->where('jornada', $jornada);
            });
        })
            ->whereDate('created_at', $fecha)
            ->with(['caracterizacion.ficha', 'caracterizacion.jornada'])
            ->get();
    }

    public function buscarAsistencia(int $caracterizacionId, string $numeroIdentificacion, string $horaIngreso): ?AsistenciaAprendiz
    {
        $horaIngresoFormateada = \Carbon\Carbon::parse($horaIngreso)->format('H:i:s');

        return AsistenciaAprendiz::where('caracterizacion_id', $caracterizacionId)
            ->where('numero_identificacion', $numeroIdentificacion)
            ->whereTime('hora_ingreso', $horaIngresoFormateada)
            ->first();
    }
}
