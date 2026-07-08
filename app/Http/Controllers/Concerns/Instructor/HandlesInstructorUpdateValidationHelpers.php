<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorUpdateValidationHelpers
{
    private function validateUpdateForeignKeyFields(array &$datos): void
    {
        if (isset($datos['tipo_vinculacion_id']) && ! empty($datos['tipo_vinculacion_id'])) {
            $parametroTema = \App\Models\ParametroTema::find($datos['tipo_vinculacion_id']);
            if (! $parametroTema) {
                $datos['tipo_vinculacion_id'] = null;
            }
        } else {
            $datos['tipo_vinculacion_id'] = null;
        }

        if (isset($datos['nivel_academico_id']) && ! empty($datos['nivel_academico_id'])) {
            $parametroTema = \App\Models\ParametroTema::find($datos['nivel_academico_id']);
            if (! $parametroTema) {
                $datos['nivel_academico_id'] = null;
            }
        } else {
            $datos['nivel_academico_id'] = null;
        }

        if (isset($datos['centro_formacion_id']) && ! empty($datos['centro_formacion_id'])) {
            $centro = \App\Models\CentroFormacion::find($datos['centro_formacion_id']);
            if (! $centro) {
                $datos['centro_formacion_id'] = null;
            }
        } else {
            $datos['centro_formacion_id'] = null;
        }
    }

    private function buildInstructorUpdatePayload(Instructor $instructor, array $datos): array
    {
        $fillable = $instructor->getFillable();
        $datosActualizar = array_intersect_key($datos, array_flip($fillable));
        $datosActualizar['user_edit_id'] = Auth::id();

        Log::info('Datos a actualizar en instructor', [
            'instructor_id' => $instructor->id,
            'campos_json' => [
                'titulos_obtenidos' => $datosActualizar['titulos_obtenidos'] ?? null,
                'instituciones_educativas' => $datosActualizar['instituciones_educativas'] ?? null,
                'certificaciones_tecnicas' => $datosActualizar['certificaciones_tecnicas'] ?? null,
                'cursos_complementarios' => $datosActualizar['cursos_complementarios'] ?? null,
                'idiomas' => $datosActualizar['idiomas'] ?? null,
            ],
        ]);

        return $datosActualizar;
    }

    private function logInstructorAfterUpdate(Instructor $instructor): void
    {
        $instructor->refresh();
        Log::info('Instructor actualizado - valores guardados', [
            'instructor_id' => $instructor->id,
            'titulos_obtenidos' => $instructor->titulos_obtenidos,
            'instituciones_educativas' => $instructor->instituciones_educativas,
            'certificaciones_tecnicas' => $instructor->certificaciones_tecnicas,
            'cursos_complementarios' => $instructor->cursos_complementarios,
            'idiomas' => $instructor->idiomas,
        ]);
    }
}
