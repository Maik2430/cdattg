<?php

namespace App\Models\Concerns\AsignacionInstructorLog;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

trait ManagesAsignacionInstructorLogQueries
{
    /**
     * Crear log de asignación
     *
     * @param  int|null  $instructorId  ID del instructor (null para errores generales)
     * @param  int  $fichaId  ID de la ficha
     * @param  string  $accion  Acción realizada (asignar, desasignar, editar)
     * @param  string  $resultado  Resultado (exitoso, error, advertencia)
     * @param  string  $mensaje  Mensaje descriptivo
     * @param  int  $userId  ID del usuario que realizó la acción
     * @param  array  $detalles  Detalles adicionales
     * @param  array|null  $datosAnteriores  Datos anteriores a la acción
     * @param  array|null  $datosNuevos  Datos nuevos después de la acción
     */
    public static function crearLog(
        ?int $instructorId,
        int $fichaId,
        string $accion,
        string $resultado,
        string $mensaje,
        int $userId,
        array $detalles = [],
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null
    ): self {
        return self::create([
            'instructor_id' => $instructorId,
            'ficha_id' => $fichaId,
            'accion' => $accion,
            'detalles' => $detalles,
            'resultado' => $resultado,
            'mensaje' => $mensaje,
            'user_id' => $userId,
            'fecha_accion' => now(),
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
        ]);
    }

    /**
     * Obtener logs recientes
     */
    public static function obtenerLogsRecientes(int $limite = 50): Collection
    {
        return self::with(['instructor.persona', 'ficha', 'user'])
            ->orderBy('fecha_accion', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Obtener estadísticas de asignaciones
     */
    public static function obtenerEstadisticas(?Carbon $fechaInicio = null, ?Carbon $fechaFin = null): array
    {
        $query = self::query();

        if ($fechaInicio && $fechaFin) {
            $query->entreFechas($fechaInicio, $fechaFin);
        }

        $total = $query->count();
        $exitosos = $query->clone()->exitoso()->count();
        $conError = $query->clone()->conError()->count();

        return [
            'total' => $total,
            'exitosos' => $exitosos,
            'con_error' => $conError,
            'porcentaje_exito' => $total > 0 ? round(($exitosos / $total) * 100, 2) : 0,
            'porcentaje_error' => $total > 0 ? round(($conError / $total) * 100, 2) : 0,
        ];
    }
}
