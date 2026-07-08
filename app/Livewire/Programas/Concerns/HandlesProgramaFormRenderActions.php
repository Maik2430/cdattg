<?php

namespace App\Livewire\Programas\Concerns;

use App\Models\Parametro;
use App\Models\RedConocimiento;

trait HandlesProgramaFormRenderActions
{
    public function getRedesConocimientoProperty()
    {
        return RedConocimiento::all();
    }

    public function getNivelesFormacionProperty()
    {
        return Parametro::whereIn('name', ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'])->get();
    }

    public function render()
    {
        return view('livewire.programas.programa-form');
    }
}
