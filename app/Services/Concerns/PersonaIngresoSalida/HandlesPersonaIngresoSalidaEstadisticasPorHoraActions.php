<?php

namespace App\Services\Concerns\PersonaIngresoSalida;

use App\Models\PersonaIngresoSalida;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait HandlesPersonaIngresoSalidaEstadisticasPorHoraActions
{
    /**
     * Obtiene estadísticas de entradas y salidas por hora del día
     *
     * @param  string|null  $fecha  Fecha en formato Y-m-d, si es null usa hoy
     * @param  int|null  $sedeId  ID de la sede, si es null incluye todas
     * @return array Array con las horas del día (0-23) y cantidad de entradas/salidas
     */
    public function obtenerEstadisticasPorHora(?string $fecha = null, ?int $sedeId = null): array
    {
        $fecha = $fecha ?? Carbon::today()->format('Y-m-d');

        // Inicializar arrays para todas las horas del día (0-23)
        $entradasPorHora = array_fill(0, 24, 0);
        $salidasPorHora = array_fill(0, 24, 0);

        // Query para entradas
        $queryEntradas = PersonaIngresoSalida::whereDate('fecha_entrada', $fecha);
        if ($sedeId) {
            $queryEntradas->where('sede_id', $sedeId);
        }

        // Obtener entradas agrupadas por hora
        $horaEntradaExpr = $this->horaSqlExpression('timestamp_entrada');
        $entradas = $queryEntradas
            ->select(DB::raw("{$horaEntradaExpr} as hora"), DB::raw(self::COUNT_TOTAL))
            ->groupBy(DB::raw($horaEntradaExpr))
            ->get();

        foreach ($entradas as $entrada) {
            $hora = (int) $entrada->hora;
            if ($hora >= 0 && $hora <= 23) {
                $entradasPorHora[$hora] = (int) $entrada->total;
            }
        }

        // Query para salidas
        $querySalidas = PersonaIngresoSalida::whereDate('fecha_salida', $fecha)
            ->whereNotNull('timestamp_salida');
        if ($sedeId) {
            $querySalidas->where('sede_id', $sedeId);
        }

        // Obtener salidas agrupadas por hora
        $horaSalidaExpr = $this->horaSqlExpression('timestamp_salida');
        $salidas = $querySalidas
            ->select(DB::raw("{$horaSalidaExpr} as hora"), DB::raw(self::COUNT_TOTAL))
            ->groupBy(DB::raw($horaSalidaExpr))
            ->get();

        foreach ($salidas as $salida) {
            $hora = (int) $salida->hora;
            if ($hora >= 0 && $hora <= 23) {
                $salidasPorHora[$hora] = (int) $salida->total;
            }
        }

        // Formatear etiquetas de horas
        $horasLabels = [];
        for ($i = 0; $i < 24; $i++) {
            $horasLabels[] = sprintf('%02d:00', $i);
        }

        return [
            'fecha' => $fecha,
            'sede_id' => $sedeId,
            'horas' => $horasLabels,
            'entradas' => $entradasPorHora,
            'salidas' => $salidasPorHora,
        ];
    }

    private function horaSqlExpression(string $column): string
    {
        if (DB::getDriverName() === 'sqlite') {
            return "CAST(strftime('%H', {$column}) AS INTEGER)";
        }

        return "HOUR({$column})";
    }
}
