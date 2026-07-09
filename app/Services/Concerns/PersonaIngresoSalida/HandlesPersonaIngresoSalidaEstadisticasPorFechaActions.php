<?php

namespace App\Services\Concerns\PersonaIngresoSalida;

use App\Models\PersonaIngresoSalida;
use Illuminate\Support\Facades\DB;

trait HandlesPersonaIngresoSalidaEstadisticasPorFechaActions
{
    /**
     * Obtiene estadísticas detalladas por fecha
     */
    public function obtenerEstadisticasPorFecha(string $fecha, ?int $sedeId = null): array
    {
        $queryEntradas = PersonaIngresoSalida::porFecha($fecha);
        $querySalidas = PersonaIngresoSalida::porFecha($fecha)->whereNotNull('timestamp_salida');
        $queryDentro = PersonaIngresoSalida::porFecha($fecha)->dentro();

        if ($sedeId) {
            $queryEntradas->porSede($sedeId);
            $querySalidas->porSede($sedeId);
            $queryDentro->porSede($sedeId);
        }

        $entradas = $queryEntradas
            ->select('tipo_persona', DB::raw(self::COUNT_TOTAL))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        $salidas = $querySalidas
            ->select('tipo_persona', DB::raw(self::COUNT_TOTAL))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        $dentro = $queryDentro
            ->select('tipo_persona', DB::raw(self::COUNT_TOTAL))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        return [
            'fecha' => $fecha,
            'sede_id' => $sedeId,
            'entradas' => [
                'instructores' => $entradas['instructor'] ?? 0,
                'aprendices' => $entradas['aprendiz'] ?? 0,
                'visitantes' => $entradas['visitante'] ?? 0,
                'administrativos' => $entradas['administrativo'] ?? 0,
                'aspirantes' => $entradas['aspirante'] ?? 0,
                'super_administradores' => $entradas['super_administrador'] ?? 0,
                'total' => array_sum($entradas),
            ],
            'salidas' => [
                'instructores' => $salidas['instructor'] ?? 0,
                'aprendices' => $salidas['aprendiz'] ?? 0,
                'visitantes' => $salidas['visitante'] ?? 0,
                'administrativos' => $salidas['administrativo'] ?? 0,
                'aspirantes' => $salidas['aspirante'] ?? 0,
                'super_administradores' => $salidas['super_administrador'] ?? 0,
                'total' => array_sum($salidas),
            ],
            'dentro' => [
                'instructores' => $dentro['instructor'] ?? 0,
                'aprendices' => $dentro['aprendiz'] ?? 0,
                'visitantes' => $dentro['visitante'] ?? 0,
                'administrativos' => $dentro['administrativo'] ?? 0,
                'aspirantes' => $dentro['aspirante'] ?? 0,
                'super_administradores' => $dentro['super_administrador'] ?? 0,
                'total' => array_sum($dentro),
            ],
        ];
    }

    /**
     * Obtiene estadísticas por sede
     */
    public function obtenerEstadisticasPorSede(int $sedeId): array
    {
        $estadisticasHoy = $this->obtenerEstadisticasPersonasDentroHoy($sedeId);
        $estadisticasGenerales = $this->obtenerEstadisticasPersonasDentro($sedeId);

        return [
            'sede_id' => $sedeId,
            'hoy' => $estadisticasHoy,
            'total_dentro' => $estadisticasGenerales,
        ];
    }
}
