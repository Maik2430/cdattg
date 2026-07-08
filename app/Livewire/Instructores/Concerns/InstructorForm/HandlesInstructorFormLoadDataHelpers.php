<?php

namespace App\Livewire\Instructores\Concerns\InstructorForm;

trait HandlesInstructorFormLoadDataHelpers
{
    private function loadInstructorData(): void
    {
        if (! $this->instructor) {
            return;
        }

        $this->persona_id = $this->instructor->persona_id;
        $this->regional_id = $this->instructor->regional_id;
        $this->centro_formacion_id = $this->instructor->centro_formacion_id;
        $this->tipo_vinculacion_id = $this->instructor->tipo_vinculacion_id;
        $this->jornadas = $this->instructor->jornadas ?? [];

        $this->fecha_ingreso_sena = $this->instructor->fecha_ingreso_sena ?
            $this->instructor->fecha_ingreso_sena->format('Y-m-d') : null;

        $this->anos_experiencia = $this->instructor->anos_experiencia;
        $this->experiencia_instructor_meses = $this->instructor->experiencia_instructor_meses;
        $this->experiencia_laboral = $this->instructor->experiencia_laboral;

        $this->nivel_academico_id = $this->instructor->nivel_academico_id;
        $this->formacion_pedagogia = $this->instructor->formacion_pedagogia;
        $this->titulos_obtenidos = $this->instructor->titulos_obtenidos ?? [''];
        $this->instituciones_educativas = $this->instructor->instituciones_educativas ?? [''];
        $this->certificaciones_tecnicas = $this->instructor->certificaciones_tecnicas ?? [''];
        $this->cursos_complementarios = $this->instructor->cursos_complementarios ?? [''];

        $this->areas_experticia = $this->instructor->areas_experticia ?? [''];
        $this->competencias_tic = $this->instructor->competencias_tic ?? [''];
        $this->idiomas = $this->instructor->idiomas ?? [['idioma' => '', 'nivel' => '']];
        $this->modalidades = $this->instructor->modalidades ?? [];
        $this->especialidades = $this->instructor->especialidades ?? [];

        $this->numero_contrato = $this->instructor->numero_contrato;

        $this->fecha_inicio_contrato = $this->instructor->fecha_inicio_contrato ?
            $this->instructor->fecha_inicio_contrato->format('Y-m-d') : null;

        $this->fecha_fin_contrato = $this->instructor->fecha_fin_contrato ?
            $this->instructor->fecha_fin_contrato->format('Y-m-d') : null;

        $this->supervisor_contrato = $this->instructor->supervisor_contrato;
    }
}
