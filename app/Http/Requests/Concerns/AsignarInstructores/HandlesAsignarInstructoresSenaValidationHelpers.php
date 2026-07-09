<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

use App\Models\Competencia;
use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\ResultadosAprendizaje;

trait HandlesAsignarInstructoresSenaValidationHelpers
{
    private function validarEspecialidadesRequeridas($validator): void {}

    private function validarDisponibilidadHoraria($validator): void {}

    private function validarReglasSENA($validator): void
    {
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::with('sede.regional')->find($fichaId);
        $instructores = $this->input('instructores', []);

        foreach ($instructores as $index => $instructorData) {
            $instructor = Instructor::find($instructorData['instructor_id']);
            if (! $instructor) {
                continue;
            }

            $fichaRegionalId = $ficha && $ficha->sede ? $ficha->sede->regional_id : null;
            if ($ficha && $fichaRegionalId && $instructor->regional_id !== $fichaRegionalId) {
                $validator->errors()->add(
                    "instructores.{$index}.instructor_id",
                    "El instructor {$instructor->nombre_completo} debe pertenecer a la misma regional que la ficha."
                );
            }
        }
    }

    private function validarCompetenciaPerteneceAPrograma($competenciaId, $fail, $attribute): void
    {
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::with('programaFormacion.competencias')->find($fichaId);

        if (! $ficha || ! $ficha->programaFormacion) {
            $fail('La ficha no tiene un programa de formación asociado.');

            return;
        }

        $competenciaPertenece = $ficha->programaFormacion->competencias->contains('id', $competenciaId);

        if (! $competenciaPertenece) {
            $competencia = Competencia::find($competenciaId);
            $competenciaNombre = $competencia ? $competencia->nombre : 'Competencia desconocida';
            $fail("La competencia '{$competenciaNombre}' no pertenece al programa de formación de esta ficha.");
        }
    }

    private function validarResultadoPerteneceACompetencia($resultadoId, $fail, $attribute): void
    {
        preg_match('/instructores\.(\d+)\.resultados_aprendizaje\.\d+/', $attribute, $matches);
        $instructorIndex = $matches[1] ?? null;

        if ($instructorIndex === null) {
            return;
        }

        $instructorData = $this->input("instructores.{$instructorIndex}", []);
        $competenciaId = $instructorData['competencia_id'] ?? null;

        if (! $competenciaId) {
            $fail('Debe seleccionar una competencia antes de seleccionar resultados de aprendizaje.');

            return;
        }

        $competencia = Competencia::with('resultadosAprendizaje')->find($competenciaId);

        if (! $competencia) {
            $fail('La competencia seleccionada no existe.');

            return;
        }

        $resultadoPertenece = $competencia->resultadosAprendizaje->contains('id', $resultadoId);

        if (! $resultadoPertenece) {
            $resultado = ResultadosAprendizaje::find($resultadoId);
            $resultadoNombre = $resultado ? $resultado->nombre : 'Resultado desconocido';
            $fail("El resultado de aprendizaje '{$resultadoNombre}' no pertenece a la competencia seleccionada.");
        }
    }
}
