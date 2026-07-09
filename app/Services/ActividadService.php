<?php

namespace App\Services;

use App\Models\RegistroActividades;
use App\Repositories\RegistroActividadesRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActividadService
{
    protected RegistroActividadesRepository $repository;

    public function __construct(RegistroActividadesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Obtiene actividades por instructor
     */
    public function obtenerPorInstructor(int $instructorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->obtenerPorInstructor($instructorId, $perPage);
    }

    /**
     * Registra una nueva actividad
     */
    public function registrar(array $datos): RegistroActividades
    {
        return DB::transaction(function () use ($datos) {
            $actividad = $this->repository->crear($datos);

            Log::info('Actividad registrada', [
                'actividad_id' => $actividad->id,
                'instructor_id' => $datos['instructor_id'],
                'ficha_id' => $datos['ficha_caracterizacion_id'] ?? null,
            ]);

            return $actividad;
        });
    }

    /**
     * Actualiza una actividad
     */
    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = $this->repository->actualizar($id, $datos);

            if ($actualizado) {
                Log::info('Actividad actualizada', [
                    'actividad_id' => $id,
                ]);
            }

            return $actualizado;
        });
    }

    /**
     * Elimina una actividad
     */
    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $eliminado = $this->repository->eliminar($id);

            if ($eliminado) {
                Log::info('Actividad eliminada', [
                    'actividad_id' => $id,
                ]);
            }

            return $eliminado;
        });
    }

    /**
     * Obtiene reporte de actividades por período
     */
    public function obtenerReportePeriodo(string $fechaInicio, string $fechaFin, ?int $instructorId = null): array
    {
        $actividades = $this->repository->obtenerPorFechas($fechaInicio, $fechaFin, $instructorId);

        return [
            'total_actividades' => $actividades->count(),
            'por_instructor' => $actividades->groupBy('instructor_id')->map(function ($grupo) {
                return [
                    'instructor' => $grupo->first()->instructor->persona->nombre_completo ?? 'N/A',
                    'total' => $grupo->count(),
                ];
            }),
            'por_ficha' => $actividades->groupBy('ficha_caracterizacion_id')->map(function ($grupo) {
                return [
                    'ficha' => $grupo->first()->fichaCaracterizacion->ficha ?? 'N/A',
                    'total' => $grupo->count(),
                ];
            }),
        ];
    }
}
