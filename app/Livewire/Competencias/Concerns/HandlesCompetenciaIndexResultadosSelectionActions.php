<?php

namespace App\Livewire\Competencias\Concerns;

trait HandlesCompetenciaIndexResultadosSelectionActions
{
    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->resultadosSeleccionados = $this->getResultadosDisponibles()->pluck('id')->toArray();
        } else {
            $this->resultadosSeleccionados = [];
        }
    }
}
