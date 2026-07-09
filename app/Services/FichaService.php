<?php

namespace App\Services;

use App\Events\FichaAsignadaAInstructor;
use App\Models\FichaCaracterizacion;
use App\Repositories\FichaRepository;
use App\Repositories\InstructorFichaRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FichaService
{
    protected FichaRepository $fichaRepo;

    protected InstructorFichaRepository $instructorFichaRepo;

    public function __construct(
        FichaRepository $fichaRepo,
        InstructorFichaRepository $instructorFichaRepo
    ) {
        $this->fichaRepo = $fichaRepo;
        $this->instructorFichaRepo = $instructorFichaRepo;
    }

    /**
     * Lista fichas con filtros
     */
    public function listarConFiltros(array $filtros = []): LengthAwarePaginator
    {
        return $this->fichaRepo->obtenerConFiltros($filtros);
    }

    /**
     * Obtiene ficha con relaciones
     */
    public function obtener(int $id): ?FichaCaracterizacion
    {
        return $this->fichaRepo->encontrarConRelaciones($id);
    }

    /**
     * Crea una nueva ficha
     */
    public function crear(array $datos): FichaCaracterizacion
    {
        return DB::transaction(function () use ($datos) {
            $ficha = $this->fichaRepo->crear($datos);

            Log::info('Ficha creada exitosamente', [
                'ficha_id' => $ficha->id,
                'numero' => $ficha->ficha,
            ]);

            return $ficha;
        });
    }

    /**
     * Actualiza una ficha
     */
    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = $this->fichaRepo->actualizar($id, $datos);

            Log::info('Ficha actualizada', [
                'ficha_id' => $id,
            ]);

            return $actualizado;
        });
    }

    /**
     * Asigna instructor a ficha
     */
    public function asignarInstructor(int $fichaId, int $instructorId, array $datosAsignacion): bool
    {
        return DB::transaction(function () use ($fichaId, $instructorId, $datosAsignacion) {
            // Verificar si ya está asignado
            if ($this->instructorFichaRepo->estaAsignado($instructorId, $fichaId)) {
                throw new \Exception('El instructor ya está asignado a esta ficha.');
            }

            // Crear asignación
            $asignacion = $this->instructorFichaRepo->crear([
                'instructor_id' => $instructorId,
                'ficha_caracterizacion_id' => $fichaId,
                ...$datosAsignacion,
            ]);

            $instructor = \App\Models\Instructor::find($instructorId);
            $ficha = $this->fichaRepo->encontrarConRelaciones($fichaId);

            // Disparar evento
            event(new FichaAsignadaAInstructor($instructor, $ficha, $datosAsignacion));

            Log::info('Instructor asignado a ficha', [
                'ficha_id' => $fichaId,
                'instructor_id' => $instructorId,
            ]);

            return true;
        });
    }

    /**
     * Obtiene estadísticas de fichas
     */
    public function obtenerEstadisticas(): array
    {
        return $this->fichaRepo->obtenerEstadisticas();
    }

    /**
     * Verifica disponibilidad de ficha
     */
    public function verificarDisponibilidad(int $fichaId): array
    {
        $ficha = $this->fichaRepo->encontrarConRelaciones($fichaId);

        if (! $ficha) {
            return [
                'disponible' => false,
                'razon' => 'Ficha no encontrada',
            ];
        }

        $instructores = $this->instructorFichaRepo->obtenerPorFicha($fichaId);
        $aprendices = \App\Models\Aprendiz::where('ficha_caracterizacion_id', $fichaId)
            ->whereNull('deleted_at')
            ->get();

        return [
            'disponible' => $ficha->status,
            'tiene_instructor' => $instructores->isNotEmpty(),
            'total_instructores' => $instructores->count(),
            'total_aprendices' => $aprendices->count(),
            'cupos_disponibles' => max(0, ($ficha->cupos_maximos ?? 40) - $aprendices->count()),
        ];
    }
}
