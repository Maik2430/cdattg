<?php

namespace App\Livewire\Instructores\Concerns\InstructorForm;

use Illuminate\Support\Facades\Auth;

trait HandlesInstructorFormPrepareDataHelpers
{
    private function prepareDataForService(): array
    {
        return [
            'persona_id' => $this->persona_id,
            'regional_id' => $this->regional_id,
            'centro_formacion_id' => $this->centro_formacion_id,
            'tipo_vinculacion_id' => $this->tipo_vinculacion_id,
            'jornadas' => $this->jornadas,
            'fecha_ingreso_sena' => $this->fecha_ingreso_sena,
            'anos_experiencia' => $this->anos_experiencia,
            'experiencia_instructor_meses' => $this->experiencia_instructor_meses,
            'experiencia_laboral' => $this->experiencia_laboral,
            'nivel_academico_id' => $this->nivel_academico_id,
            'formacion_pedagogia' => $this->formacion_pedagogia,
            'titulos_obtenidos' => $this->cleanArray($this->titulos_obtenidos),
            'instituciones_educativas' => $this->cleanArray($this->instituciones_educativas),
            'certificaciones_tecnicas' => $this->cleanArray($this->certificaciones_tecnicas),
            'cursos_complementarios' => $this->cleanArray($this->cursos_complementarios),
            'areas_experticia' => $this->cleanArray($this->areas_experticia),
            'competencias_tic' => $this->cleanArray($this->competencias_tic),
            'idiomas' => $this->cleanIdiomasArray($this->idiomas),
            'modalidades' => $this->modalidades,
            'especialidades' => $this->especialidades,
            'numero_contrato' => $this->numero_contrato,
            'fecha_inicio_contrato' => $this->fecha_inicio_contrato,
            'fecha_fin_contrato' => $this->fecha_fin_contrato,
            'supervisor_contrato' => $this->supervisor_contrato,
            'user_create_id' => Auth::id(),
            'user_edit_id' => Auth::id(),
        ];
    }

    private function cleanArray(array $array): array
    {
        return array_values(array_filter(array_map('trim', $array), function ($value) {
            return $value !== '' && $value !== null;
        }));
    }

    private function cleanIdiomasArray(array $idiomas): array
    {
        return array_values(array_filter($idiomas, function ($idioma) {
            return ! empty($idioma['idioma']) && ! empty($idioma['nivel']);
        }));
    }
}
