<?php

namespace App\Livewire\Instructores\Concerns\InstructorForm;

use App\Models\CentroFormacion;
use App\Models\Regional;

trait HandlesInstructorFormComputedProperties
{
    public function getRegionalNombreProperty(): string
    {
        if ($this->regional_id) {
            $regional = Regional::find($this->regional_id);

            return $regional ? $regional->nombre : '';
        }

        return '';
    }

    public function getCentroFormacionNombreProperty(): string
    {
        if ($this->centro_formacion_id) {
            $centro = CentroFormacion::find($this->centro_formacion_id);

            return $centro ? $centro->nombre : '';
        }

        return '';
    }
}
