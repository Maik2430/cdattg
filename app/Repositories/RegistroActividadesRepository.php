<?php

namespace App\Repositories;

use App\Models\RegistroActividades;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RegistroActividadesRepository
{
    /**
     * Obtiene actividades por instructor
     */
    public function obtenerPorInstructor(int $instructorId, int $perPage = 15): LengthAwarePaginator
    {
        return RegistroActividades::where('instructor_id', $instructorId)
            ->with(['instructor.persona', 'fichaCaracterizacion'])
            ->orderBy('fecha', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtiene actividades por ficha
     */
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return RegistroActividades::where('ficha_caracterizacion_id', $fichaId)
            ->with(['instructor.persona'])
            ->orderBy('fecha', 'desc')
            ->get();
    }

    /**
     * Obtiene actividades por rango de fechas
     */
    public function obtenerPorFechas(string $fechaInicio, string $fechaFin, ?int $instructorId = null): Collection
    {
        $query = RegistroActividades::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with(['instructor.persona', 'fichaCaracterizacion']);

        if ($instructorId) {
            $query->where('instructor_id', $instructorId);
        }

        return $query->orderBy('fecha', 'desc')->get();
    }

    /**
     * Crea un registro de actividad
     */
    public function crear(array $datos): RegistroActividades
    {
        return RegistroActividades::create($datos);
    }

    /**
     * Actualiza un registro
     */
    public function actualizar(int $id, array $datos): bool
    {
        return RegistroActividades::where('id', $id)->update($datos);
    }

    /**
     * Elimina un registro
     */
    public function eliminar(int $id): bool
    {
        return RegistroActividades::where('id', $id)->delete();
    }
}
