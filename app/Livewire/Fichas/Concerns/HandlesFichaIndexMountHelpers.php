<?php

namespace App\Livewire\Fichas\Concerns;

use App\Models\ProgramaFormacion;
use App\Models\Regional;
use App\Models\Sede;

trait HandlesFichaIndexMountHelpers
{
    private function loadFiltersData()
    {
        // Cargar datos para los filtros
        $this->programas = ProgramaFormacion::orderBy('nombre')->get();
        $this->regionales = Regional::orderBy('nombre')->get();
        $this->sedes = Sede::orderBy('sede')->get();
    }
}
