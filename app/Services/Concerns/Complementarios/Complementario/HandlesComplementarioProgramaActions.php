<?php

namespace App\Services\Concerns\Complementarios\Complementario;

use App\Exceptions\ProgramaNoEncontradoException;
use App\Models\Complementarios\ComplementarioOfertado;
use Illuminate\Database\Eloquent\Collection;

trait HandlesComplementarioProgramaActions
{
    /**
     * Obtener programas con relaciones necesarias para diferentes vistas.
     */
    public function obtenerProgramas(array $relations = [], ?int $estado = null): Collection
    {
        if (! is_null($estado)) {
            return $this->programaRepository->getByEstado($estado, $relations);
        }

        return $this->programaRepository->getAll($relations);
    }

    /**
     * Sincronizar días de formación garantizando atomicidad.
     */
    public function sincronizarDiasFormacion(ComplementarioOfertado $programa, ?array $dias): void
    {
        if (empty($dias)) {
            $programa->diasFormacion()->detach();

            return;
        }

        $programa->diasFormacion()->sync(
            collect($dias)->mapWithKeys(static function (array $dia): array {
                return [
                    $dia['dia_id'] => [
                        'hora_inicio' => $dia['hora_inicio'],
                        'hora_fin' => $dia['hora_fin'],
                    ],
                ];
            })->all()
        );
    }

    /**
     * Obtener estadísticas básicas de un programa
     */
    public function obtenerEstadisticasPrograma(int $programaId): array
    {
        $programa = $this->programaRepository->findWithRelations($programaId);

        if (! $programa) {
            throw new ProgramaNoEncontradoException('Programa no encontrado');
        }

        $totalAspirantes = $this->aspiranteRepository->countByPrograma($programaId);
        $aspirantesActivos = $this->aspiranteRepository->countByEstado($programaId, 1);
        $aspirantesAceptados = $this->aspiranteRepository->countByEstado($programaId, 3);

        return [
            'total_aspirantes' => $totalAspirantes,
            'aspirantes_activos' => $aspirantesActivos,
            'aspirantes_aceptados' => $aspirantesAceptados,
            'cupos_disponibles' => max(0, $programa->cupos - $totalAspirantes),
        ];
    }
}
