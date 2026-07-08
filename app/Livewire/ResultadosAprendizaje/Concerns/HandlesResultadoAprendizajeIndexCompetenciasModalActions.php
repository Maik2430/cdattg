<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesResultadoAprendizajeIndexCompetenciasModalActions
{
    public function openCompetenciasModal($resultadoId)
    {
        $this->selectedResultado = ResultadosAprendizaje::with(['competencias'])->find($resultadoId);
        $this->showCompetenciasModal = true;

        if ($this->showShowModal) {
            $this->showShowModal = false;
        }
    }

    public function closeCompetenciasModal()
    {
        $this->showCompetenciasModal = false;
        $this->selectedResultado = null;
    }

    public function refreshCompetencias()
    {
        $this->searchCompetencias = '';
        $this->competenciasSeleccionadas = [];
        $this->selectAll = false;
    }
}
