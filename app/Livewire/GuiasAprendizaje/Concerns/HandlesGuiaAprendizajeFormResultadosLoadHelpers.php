<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesGuiaAprendizajeFormResultadosLoadHelpers
{
    private function cargarResultadosDisponibles()
    {
        $query = ResultadosAprendizaje::orderBy('codigo');

        if ($this->isEdit && ! empty($this->resultadosSeleccionados)) {
            $query->whereNotIn('id', $this->resultadosSeleccionados);
        }

        if ($this->searchResultado) {
            $query->where(function ($q) {
                $q->where('codigo', 'like', '%'.$this->searchResultado.'%')
                    ->orWhere('nombre', 'like', '%'.$this->searchResultado.'%');
            });
        }

        $this->resultadosDisponibles = $query->get();
    }
}
