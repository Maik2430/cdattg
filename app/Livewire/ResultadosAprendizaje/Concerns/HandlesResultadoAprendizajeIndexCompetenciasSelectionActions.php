<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

trait HandlesResultadoAprendizajeIndexCompetenciasSelectionActions
{
    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->competenciasSeleccionadas = $this->getCompetenciasDisponibles()->pluck('id')->toArray();
        } else {
            $this->competenciasSeleccionadas = [];
        }
    }
}
