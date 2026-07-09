<?php

namespace App\Services\Concerns\Asistencia;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

trait HandlesAsistenciaReadActions
{
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return $this->repository->obtenerPorFicha($fichaId);
    }

    public function obtenerPorFichaYFechas(int $fichaId, string $fechaInicio, string $fechaFin): Collection
    {
        return $this->repository->obtenerPorFichaYFechas($fichaId, $fechaInicio, $fechaFin);
    }

    public function obtenerPorDocumento(string $numeroDocumento): Collection
    {
        return $this->repository->obtenerPorDocumento($numeroDocumento);
    }

    public function obtenerDocumentosPorFicha(int $fichaId): Collection
    {
        return $this->repository->obtenerDocumentosPorFicha($fichaId);
    }

    public function obtenerListaDelDia(string $ficha, string $jornada): ?Collection
    {
        $fechaActual = Carbon::now()->format('Y-m-d');
        $horaActual = Carbon::now()->format('H:i:s');

        if (! $this->jornadaValidation->validarHorarioJornada($horaActual, $jornada)) {
            return null;
        }

        return $this->repository->obtenerPorFichaJornadaYFecha($ficha, $jornada, $fechaActual);
    }

    public function obtenerEstadisticas(int $fichaId, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        return $this->repository->obtenerEstadisticas($fichaId, $fechaInicio, $fechaFin);
    }

    public function existeAsistenciaDelDia(string $numeroDocumento, ?string $fecha = null): bool
    {
        $fecha = $fecha ?? Carbon::now()->format('Y-m-d');

        return $this->repository->existeAsistenciaDelDia($numeroDocumento, $fecha);
    }
}
