<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\ProgramaFormacion;
use App\Models\ResultadosAprendizaje;

trait HandlesGuiaAprendizajeFormRenderActions
{
    public function render()
    {
        $programas = ProgramaFormacion::orderBy('nombre')->get();
        $resultadosAprendizaje = ResultadosAprendizaje::orderBy('codigo')->get();

        return view('livewire.guias-aprendizaje.guia-aprendizaje-form', compact(
            'programas',
            'resultadosAprendizaje'
        ));
    }
}
