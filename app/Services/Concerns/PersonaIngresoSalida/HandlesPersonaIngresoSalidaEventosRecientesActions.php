<?php

namespace App\Services\Concerns\PersonaIngresoSalida;

use App\Models\PersonaIngresoSalida;
use Carbon\Carbon;

trait HandlesPersonaIngresoSalidaEventosRecientesActions
{
    /**
     * Obtiene eventos recientes de ingreso/salida para una fecha específica
     *
     * @param  string  $fecha  Fecha en formato Y-m-d
     * @param  int|null  $sedeId  ID de la sede, si es null incluye todas
     * @param  int  $limite  Número máximo de eventos a retornar
     * @return array Array con eventos ordenados por fecha/hora más reciente
     */
    public function obtenerEventosRecientes(string $fecha, ?int $sedeId = null, int $limite = 20): array
    {
        $eventos = [];

        // Obtener entradas del día
        $queryEntradas = PersonaIngresoSalida::whereDate('fecha_entrada', $fecha)
            ->with(['persona', 'sede'])
            ->orderBy('timestamp_entrada', 'desc');

        if ($sedeId) {
            $queryEntradas->where('sede_id', $sedeId);
        }

        $entradas = $queryEntradas->get();

        foreach ($entradas as $entrada) {
            $eventos[] = [
                'tipo' => 'entrada',
                'persona_id' => $entrada->persona_id,
                'persona_nombre' => $entrada->persona ? $entrada->persona->nombre_completo : 'N/A',
                'timestamp' => $entrada->timestamp_entrada,
                'hora' => $entrada->hora_entrada,
                'sede_id' => $entrada->sede_id,
                'sede_nombre' => $entrada->sede ? $entrada->sede->sede : 'N/A',
            ];
        }

        // Obtener salidas del día
        $querySalidas = PersonaIngresoSalida::whereDate('fecha_salida', $fecha)
            ->whereNotNull('timestamp_salida')
            ->with(['persona', 'sede'])
            ->orderBy('timestamp_salida', 'desc');

        if ($sedeId) {
            $querySalidas->where('sede_id', $sedeId);
        }

        $salidas = $querySalidas->get();

        foreach ($salidas as $salida) {
            $eventos[] = [
                'tipo' => 'salida',
                'persona_id' => $salida->persona_id,
                'persona_nombre' => $salida->persona ? $salida->persona->nombre_completo : 'N/A',
                'timestamp' => $salida->timestamp_salida,
                'hora' => $salida->hora_salida,
                'sede_id' => $salida->sede_id,
                'sede_nombre' => $salida->sede ? $salida->sede->sede : 'N/A',
            ];
        }

        // Ordenar todos los eventos por timestamp descendente (más recientes primero)
        usort($eventos, function ($a, $b) {
            $timestampA = $a['timestamp'] instanceof Carbon
                ? $a['timestamp']->timestamp
                : Carbon::parse($a['timestamp'])->timestamp;
            $timestampB = $b['timestamp'] instanceof Carbon
                ? $b['timestamp']->timestamp
                : Carbon::parse($b['timestamp'])->timestamp;

            return $timestampB <=> $timestampA;
        });

        // Limitar resultados
        return array_slice($eventos, 0, $limite);
    }
}
