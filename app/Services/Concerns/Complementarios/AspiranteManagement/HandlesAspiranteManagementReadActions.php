<?php

namespace App\Services\Concerns\Complementarios\AspiranteManagement;

use App\Exceptions\ProgramaNoEncontradoException;
use App\Models\Complementarios\SofiaValidationProgress;
use Exception;
use Illuminate\Database\Eloquent\Collection;

trait HandlesAspiranteManagementReadActions
{
    public function obtenerProgramasParaGestion(): Collection
    {
        return $this->programaRepository->getAllWithAspirantesCount(['modalidad.parametro', 'jornada', 'diasFormacion']);
    }

    public function obtenerAspirantesPorPrograma(string $cursoNombre): array
    {
        $programa = $this->programaRepository->findByNombre($cursoNombre);

        if (! $programa) {
            abort(404, self::PROGRAMA_NO_ENCONTRADO_SIN_PUNTO);
        }

        return $this->obtenerAspirantesPorProgramaId($programa->id);
    }

    public function obtenerAspirantesPorProgramaId(int $programaId): array
    {
        $programa = $this->programaRepository->findWithRelations($programaId, ['modalidad', 'jornada', 'diasFormacion']);

        if (! $programa) {
            abort(404, self::PROGRAMA_NO_ENCONTRADO_SIN_PUNTO);
        }

        try {
            if ($programa->catalogo_id) {
                $programa->loadMissing(['catalogo.modalidad.parametro']);
            }
        } catch (Exception $e) {
            // Si falla cargar la relación, continuar sin ella (la vista maneja esto con optional())
        }

        $aspirantes = $this->aspiranteRepository->findByPrograma($programaId, ['persona', 'complementario']);

        $existingProgress = null;
        if (! app()->environment('testing')) {
            try {
                $existingProgress = SofiaValidationProgress::where('complementario_id', $programaId)
                    ->whereIn('status', [284, 285])
                    ->first();
            } catch (Exception $e) {
                \Log::debug('No se pudo verificar progreso de validación: '.$e->getMessage());
            }
        }

        return [
            'programa' => $programa,
            'aspirantes' => $aspirantes,
            'existingProgress' => $existingProgress,
        ];
    }

    public function obtenerEstadisticasPrograma(int $programaId): array
    {
        $programa = $this->programaRepository->findWithRelations($programaId);

        if (! $programa) {
            throw new ProgramaNoEncontradoException(self::PROGRAMA_NO_ENCONTRADO_SIN_PUNTO);
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
