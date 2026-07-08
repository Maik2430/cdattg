<?php

namespace App\Livewire\Instructores\Concerns\InstructorForm;

trait HandlesInstructorFormDynamicFieldActions
{
    public function addCampo(string $campo): void
    {
        switch ($campo) {
            case 'titulos_obtenidos':
            case 'instituciones_educativas':
            case 'certificaciones_tecnicas':
            case 'cursos_complementarios':
            case 'areas_experticia':
            case 'competencias_tic':
                $this->{$campo}[] = '';
                break;
            case 'idiomas':
                $this->idiomas[] = ['idioma' => '', 'nivel' => ''];
                break;
        }
    }

    public function removeCampo(string $campo, int $index): void
    {
        if (isset($this->{$campo}[$index])) {
            unset($this->{$campo}[$index]);
            $this->{$campo} = array_values($this->{$campo});
        }
    }
}
