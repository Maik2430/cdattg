<?php

namespace App\Services\Concerns\Instructor;

use App\Models\Instructor;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorCreacionRelacionesHelpers
{
    /**
     * @param  array<string, mixed>  $datos
     * @return array<string, mixed>
     */
    protected function prepararDatosInstructorCreacion(int $personaId, array $datos): array
    {
        $datosInstructor = [
            'persona_id' => $personaId,
            'regional_id' => $datos['regional_id'],
            'anos_experiencia' => $datos['anos_experiencia'] ?? null,
            'experiencia_laboral' => $datos['experiencia_laboral'] ?? null,
            'status' => true,
            'user_create_id' => $datos['user_create_id'] ?? null,
        ];

        $camposNuevos = [
            'tipo_vinculacion_id',
            'jornadas',
            'centro_formacion_id',
            'experiencia_instructor_meses',
            'fecha_ingreso_sena',
            'nivel_academico_id',
            'titulos_obtenidos',
            'instituciones_educativas',
            'certificaciones_tecnicas',
            'cursos_complementarios',
            'formacion_pedagogia',
            'areas_experticia',
            'competencias_tic',
            'idiomas',
            'habilidades_pedagogicas',
            'documentos_adjuntos',
            'numero_contrato',
            'fecha_inicio_contrato',
            'fecha_fin_contrato',
            'supervisor_contrato',
            'eps',
            'arl',
        ];

        foreach ($camposNuevos as $campo) {
            if (isset($datos[$campo])) {
                $datosInstructor[$campo] = $datos[$campo];
            }
        }

        return $datosInstructor;
    }

    /**
     * @param  array<int>  $jornadasIds
     */
    protected function sincronizarJornadasInstructorCreacion(Instructor $instructor, array $jornadasIds): void
    {
        if (empty($jornadasIds)) {
            return;
        }

        $pivotData = [];
        foreach ($jornadasIds as $jornadaId) {
            $pivotData[$jornadaId] = [
                'user_create_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        $instructor->jornadas()->sync($pivotData);

        $instructor->jornadas = $jornadasIds;
        $instructor->save();
    }

    /**
     * @param  array<int>  $modalidadesIds
     */
    protected function sincronizarModalidadesInstructorCreacion(Instructor $instructor, array $modalidadesIds): void
    {
        Log::info('Sincronizando modalidades en InstructorService', [
            'instructor_id' => $instructor->id,
            'modalidades_ids' => $modalidadesIds,
            'count' => count($modalidadesIds ?? []),
        ]);

        if (! empty($modalidadesIds)) {
            $pivotData = [];
            foreach ($modalidadesIds as $modalidadId) {
                $pivotData[$modalidadId] = [
                    'user_create_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Log::info('Sincronizando modalidades con pivot data', [
                'instructor_id' => $instructor->id,
                'pivot_data' => $pivotData,
            ]);

            $resultadoSync = $instructor->modalidades()->sync($pivotData);

            Log::info('Resultado de sync de modalidades', [
                'instructor_id' => $instructor->id,
                'resultado' => $resultadoSync,
            ]);

            $instructor->setAttribute('habilidades_pedagogicas', $modalidadesIds);
            $instructor->save();
            $instructor->refresh();

            Log::info('Modalidades guardadas en JSON', [
                'instructor_id' => $instructor->id,
                'habilidades_pedagogicas' => $instructor->habilidades_pedagogicas,
                'habilidades_pedagogicas_raw' => $instructor->getRawOriginal('habilidades_pedagogicas'),
            ]);

            return;
        }

        Log::info('No hay modalidades, limpiando campo JSON', [
            'instructor_id' => $instructor->id,
        ]);
        $instructor->habilidades_pedagogicas = null;
        $instructor->save();
    }
}
