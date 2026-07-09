<?php

namespace App\Services\Concerns\PersonaIngresoSalida;

use App\Models\PersonaIngresoSalida;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait HandlesPersonaIngresoSalidaEstadisticasDentroActions
{
    /**
     * Obtiene estadísticas de personas dentro del edificio (todas las sedes)
     */
    public function obtenerEstadisticasPersonasDentro(?int $sedeId = null): array
    {
        $query = PersonaIngresoSalida::dentro();

        if ($sedeId) {
            $query->porSede($sedeId);
        }

        // Contar personas dentro por tipo
        $personasDentro = $query
            ->select('tipo_persona', DB::raw(self::COUNT_TOTAL))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        return [
            'instructores' => $personasDentro['instructor'] ?? 0,
            'aprendices' => $personasDentro['aprendiz'] ?? 0,
            'visitantes' => $personasDentro['visitante'] ?? 0,
            'administrativos' => $personasDentro['administrativo'] ?? 0,
            'aspirantes' => $personasDentro['aspirante'] ?? 0,
            'super_administradores' => $personasDentro['super_administrador'] ?? 0,
            'total' => array_sum($personasDentro),
        ];
    }

    /**
     * Obtiene estadísticas de personas dentro del edificio HOY
     */
    public function obtenerEstadisticasPersonasDentroHoy(?int $sedeId = null): array
    {
        return $this->obtenerEstadisticasPersonasDentroPorFecha(Carbon::today()->format('Y-m-d'), $sedeId);
    }

    /**
     * Obtiene estadísticas de personas dentro del edificio en una fecha específica
     * Considera personas que entraron en o antes de esa fecha y no salieron o salieron después
     */
    public function obtenerEstadisticasPersonasDentroPorFecha(string $fecha, ?int $sedeId = null): array
    {
        $fechaCarbon = Carbon::parse($fecha)->endOfDay();

        // Personas que entraron en o antes de la fecha y:
        // - No han salido (timestamp_salida IS NULL), o
        // - Salieron después de la fecha seleccionada
        $query = PersonaIngresoSalida::where('timestamp_entrada', '<=', $fechaCarbon)
            ->where(function ($q) use ($fechaCarbon) {
                $q->whereNull('timestamp_salida')
                    ->orWhere('timestamp_salida', '>', $fechaCarbon);
            });

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        }

        $personasDentro = $query
            ->select('tipo_persona', DB::raw(self::COUNT_TOTAL))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        // Inicializar todos los tipos con 0
        $estadisticas = [];
        foreach ($this->obtenerTiposPersona() as $tipo) {
            $estadisticas[$tipo] = $personasDentro[$tipo] ?? 0;
        }

        // Mantener compatibilidad con nombres antiguos (para no romper código existente)
        $estadisticas['instructores'] = $estadisticas['instructor'] ?? 0;
        $estadisticas['aprendices'] = $estadisticas['aprendiz'] ?? 0;
        $estadisticas['visitantes'] = $estadisticas['visitante'] ?? 0;
        $estadisticas['administrativos'] = $estadisticas['administrativo'] ?? 0;
        $estadisticas['aspirantes'] = $estadisticas['aspirante'] ?? 0;
        $estadisticas['super_administradores'] = $estadisticas['super_administrador'] ?? 0;
        $estadisticas['total'] = array_sum($personasDentro);

        return $estadisticas;
    }

    /**
     * Obtiene lista de personas dentro actualmente
     */
    public function obtenerPersonasDentro(
        ?int $sedeId = null,
        ?string $tipoPersona = null
    ): \Illuminate\Database\Eloquent\Collection {
        $query = PersonaIngresoSalida::dentro()
            ->with(['persona', 'sede', 'ambiente', 'fichaCaracterizacion'])
            ->orderBy('timestamp_entrada', 'desc');

        if ($sedeId) {
            $query->porSede($sedeId);
        }

        if ($tipoPersona) {
            $query->porTipo($tipoPersona);
        }

        return $query->get();
    }
}
