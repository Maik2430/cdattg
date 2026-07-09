<?php

namespace App\Http\Controllers\Concerns\AsignacionInstructor;

use App\Models\Competencia;
use App\Models\FichaCaracterizacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait HandlesAsignacionInstructorApiActions
{
    use HandlesAsignacionInstructorApiQueryHelpers;

    public function competenciasPorFicha(FichaCaracterizacion $ficha, Request $request): JsonResponse
    {
        if (! $ficha->programaFormacion) {
            return response()->json([
                'data' => [],
            ]);
        }

        // Si estamos editando, obtener el instructor_ficha_id
        $instructorFichaId = $request->input('instructor_ficha_id');
        $competenciaAsignadaId = null;

        // Si estamos editando, obtener la competencia asignada al instructor
        if ($instructorFichaId) {
            $competenciaAsignadaId = $this->obtenerCompetenciaAsignadaId($instructorFichaId);
        }

        // Obtener todos los resultados de aprendizaje ya asignados en esta ficha
        // Excluir los resultados asignados al instructor actual si estamos editando
        $resultadosAsignados = $this->obtenerResultadosAsignadosEnFicha($ficha->id, $instructorFichaId);

        // Obtener competencias del programa que tengan resultados de aprendizaje
        $competencias = $ficha->programaFormacion->competencias()
            ->with(['resultadosAprendizaje' => function ($query) {
                $query->select('resultados_aprendizajes.id', 'resultados_aprendizajes.codigo', 'resultados_aprendizajes.nombre');
            }])
            ->select('competencias.id', 'competencias.codigo', 'competencias.nombre')
            ->orderBy('competencias.nombre')
            ->get();

        // Filtrar competencias que tengan al menos un resultado sin asignar
        // O incluir la competencia asignada al instructor actual si estamos editando
        $competenciasConResultadosSinAsignar = $competencias->filter(function ($competencia) use ($resultadosAsignados, $competenciaAsignadaId) {
            // Si es la competencia asignada al instructor actual, siempre incluirla
            if ($competenciaAsignadaId && $competencia->id == $competenciaAsignadaId) {
                return true;
            }

            // Obtener IDs de resultados de esta competencia
            $resultadosCompetencia = $competencia->resultadosAprendizaje->pluck('id')->toArray();

            // Verificar si hay algún resultado que no esté asignado
            $resultadosSinAsignar = array_diff($resultadosCompetencia, $resultadosAsignados);

            // Solo incluir si hay al menos un resultado sin asignar
            return ! empty($resultadosSinAsignar);
        })->map(function ($competencia) {
            // Retornar solo los datos necesarios
            return [
                'id' => $competencia->id,
                'codigo' => $competencia->codigo,
                'nombre' => $competencia->nombre,
            ];
        })->values();

        return response()->json([
            'data' => $competenciasConResultadosSinAsignar,
        ]);
    }

    public function resultadosPorCompetencia(Competencia $competencia, Request $request): JsonResponse
    {
        // Obtener el ficha_id y instructor_ficha_id de la solicitud (query parameters)
        $fichaId = $request->input('ficha_id');
        $instructorFichaId = $request->input('instructor_ficha_id');

        // Obtener todos los resultados de aprendizaje de la competencia
        $resultados = $competencia->resultadosAprendizaje()
            ->select('resultados_aprendizajes.id', 'resultados_aprendizajes.codigo', 'resultados_aprendizajes.nombre', 'resultados_aprendizajes.duracion')
            ->orderBy('resultados_aprendizajes.codigo')
            ->get();

        // Si se proporciona ficha_id, filtrar resultados ya asignados
        if ($fichaId) {
            // Obtener resultados ya asignados en esta ficha
            // Excluir los resultados asignados al instructor actual si estamos editando
            $resultadosAsignados = $this->obtenerResultadosAsignadosEnFicha((int) $fichaId, $instructorFichaId);

            // Filtrar solo los resultados que no estén asignados
            // O incluir los resultados asignados al instructor actual si estamos editando
            $resultados = $resultados->reject(function ($resultado) use ($resultadosAsignados, $instructorFichaId) {
                // Si estamos editando, verificar si este resultado está asignado al instructor actual
                if ($instructorFichaId) {
                    $resultadoAsignadoAlInstructor = $this->resultadoAsignadoAlInstructor(
                        (int) $instructorFichaId,
                        $resultado->id
                    );

                    // Si está asignado al instructor actual, incluirlo
                    if ($resultadoAsignadoAlInstructor) {
                        return false; // No rechazar (incluir)
                    }
                }

                // Rechazar si está asignado a otro instructor
                return in_array($resultado->id, $resultadosAsignados);
            })->values();
        }

        return response()->json([
            'data' => $resultados,
        ]);
    }
}
