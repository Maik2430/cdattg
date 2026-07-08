<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

trait HandlesGuiaAprendizajeIndexResultadosSelectionActions
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
