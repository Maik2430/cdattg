<?php

namespace App\Livewire\Concerns\CreateInstructor;

trait HandlesCreateInstructorDynamicFieldActions
{
    public function agregarTitulo(): void
    {
        $this->titulos_obtenidos[] = '';
    }

    public function eliminarTitulo(int $index): void
    {
        unset($this->titulos_obtenidos[$index]);
        $this->titulos_obtenidos = array_values($this->titulos_obtenidos);
    }

    public function agregarInstitucion(): void
    {
        $this->instituciones_educativas[] = '';
    }

    public function eliminarInstitucion(int $index): void
    {
        unset($this->instituciones_educativas[$index]);
        $this->instituciones_educativas = array_values($this->instituciones_educativas);
    }

    public function agregarCertificacion(): void
    {
        $this->certificaciones_tecnicas[] = '';
    }

    public function eliminarCertificacion(int $index): void
    {
        unset($this->certificaciones_tecnicas[$index]);
        $this->certificaciones_tecnicas = array_values($this->certificaciones_tecnicas);
    }

    public function agregarCurso(): void
    {
        $this->cursos_complementarios[] = '';
    }

    public function eliminarCurso(int $index): void
    {
        unset($this->cursos_complementarios[$index]);
        $this->cursos_complementarios = array_values($this->cursos_complementarios);
    }

    public function agregarAreaExperticia(): void
    {
        $this->areas_experticia[] = '';
    }

    public function eliminarAreaExperticia(int $index): void
    {
        unset($this->areas_experticia[$index]);
        $this->areas_experticia = array_values($this->areas_experticia);
    }

    public function agregarCompetenciaTic(): void
    {
        $this->competencias_tic[] = '';
    }

    public function eliminarCompetenciaTic(int $index): void
    {
        unset($this->competencias_tic[$index]);
        $this->competencias_tic = array_values($this->competencias_tic);
    }

    public function agregarIdioma(): void
    {
        $this->idiomas[] = ['idioma' => '', 'nivel' => ''];
    }

    public function eliminarIdioma(int $index): void
    {
        unset($this->idiomas[$index]);
        $this->idiomas = array_values($this->idiomas);
    }
}
