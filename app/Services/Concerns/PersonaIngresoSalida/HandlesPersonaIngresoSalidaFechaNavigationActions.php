<?php

namespace App\Services\Concerns\PersonaIngresoSalida;

use App\Models\PersonaIngresoSalida;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait HandlesPersonaIngresoSalidaFechaNavigationActions
{
    /**
     * Obtiene lista de fechas que tienen registros (entradas o salidas)
     *
     * @return array Array de fechas en formato Y-m-d
     */
    public function obtenerFechasConRegistros(): array
    {
        return PersonaIngresoSalida::select(DB::raw('DISTINCT DATE(fecha_entrada) as fecha'))
            ->orderBy('fecha', 'desc')
            ->pluck('fecha')
            ->map(function ($fecha) {
                return is_string($fecha) ? $fecha : $fecha->format('Y-m-d');
            })
            ->toArray();
    }

    /**
     * Verifica si una fecha tiene registros
     *
     * @param  string  $fecha  Fecha en formato Y-m-d
     */
    public function fechaTieneRegistros(string $fecha): bool
    {
        return PersonaIngresoSalida::whereDate('fecha_entrada', $fecha)->exists();
    }

    /**
     * Obtiene la fecha anterior más cercana que tenga registros
     *
     * @param  string  $fecha  Fecha en formato Y-m-d
     * @return string|null Fecha en formato Y-m-d o null si no hay fechas anteriores
     */
    public function obtenerFechaAnteriorConRegistros(string $fecha): ?string
    {
        $fechaCarbon = Carbon::parse($fecha);
        $fechaAnterior = PersonaIngresoSalida::select(DB::raw('DATE(fecha_entrada) as fecha'))
            ->whereDate('fecha_entrada', '<', $fechaCarbon)
            ->orderBy('fecha', 'desc')
            ->first();

        if (! $fechaAnterior) {
            return null;
        }

        $fechaResultado = $fechaAnterior->fecha;

        return is_string($fechaResultado) ? $fechaResultado : $fechaResultado->format('Y-m-d');
    }

    /**
     * Obtiene la fecha siguiente más cercana que tenga registros
     *
     * @param  string  $fecha  Fecha en formato Y-m-d
     * @return string|null Fecha en formato Y-m-d o null si no hay fechas siguientes
     */
    public function obtenerFechaSiguienteConRegistros(string $fecha): ?string
    {
        $fechaCarbon = Carbon::parse($fecha);
        $hoy = Carbon::today();

        // No permitir fechas futuras
        if ($fechaCarbon->isSameDay($hoy)) {
            return null;
        }

        $fechaSiguiente = PersonaIngresoSalida::select(DB::raw('DATE(fecha_entrada) as fecha'))
            ->whereDate('fecha_entrada', '>', $fechaCarbon)
            ->whereDate('fecha_entrada', '<=', $hoy)
            ->orderBy('fecha', 'asc')
            ->first();

        if (! $fechaSiguiente) {
            return null;
        }

        $fechaResultado = $fechaSiguiente->fecha;

        return is_string($fechaResultado) ? $fechaResultado : $fechaResultado->format('Y-m-d');
    }
}
