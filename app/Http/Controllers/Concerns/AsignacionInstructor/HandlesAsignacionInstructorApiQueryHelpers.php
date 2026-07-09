<?php

namespace App\Http\Controllers\Concerns\AsignacionInstructor;

trait HandlesAsignacionInstructorApiQueryHelpers
{
    protected function obtenerCompetenciaAsignadaId(?string $instructorFichaId): ?int
    {
        if (! $instructorFichaId) {
            return null;
        }

        $instructorFicha = \App\Models\InstructorFichaCaracterizacion::find($instructorFichaId);
        if ($instructorFicha && $instructorFicha->competencia_id) {
            return $instructorFicha->competencia_id;
        }

        return null;
    }

    /**
     * @return array<int, mixed>
     */
    protected function obtenerResultadosAsignadosEnFicha(int $fichaId, ?string $instructorFichaId): array
    {
        $queryResultadosAsignados = \DB::table('instructor_ficha_resultados_aprendizaje')
            ->join('instructor_fichas_caracterizacion', 'instructor_ficha_resultados_aprendizaje.instructor_ficha_id', '=', 'instructor_fichas_caracterizacion.id')
            ->where('instructor_fichas_caracterizacion.ficha_id', $fichaId);

        if ($instructorFichaId) {
            $queryResultadosAsignados->where('instructor_fichas_caracterizacion.id', '!=', $instructorFichaId);
        }

        return $queryResultadosAsignados
            ->pluck('instructor_ficha_resultados_aprendizaje.resultado_aprendizaje_id')
            ->toArray();
    }

    protected function resultadoAsignadoAlInstructor(int $instructorFichaId, int $resultadoId): bool
    {
        return \DB::table('instructor_ficha_resultados_aprendizaje')
            ->where('instructor_ficha_id', $instructorFichaId)
            ->where('resultado_aprendizaje_id', $resultadoId)
            ->exists();
    }
}
