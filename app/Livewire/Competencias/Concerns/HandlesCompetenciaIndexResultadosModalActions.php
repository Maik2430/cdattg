<?php

namespace App\Livewire\Competencias\Concerns;

use App\Models\Competencia;

trait HandlesCompetenciaIndexResultadosModalActions
{
    public function openResultadosModal($competenciaId)
    {
        $this->selectedCompetencia = Competencia::with(['resultadosAprendizaje'])->find($competenciaId);
        $this->showResultadosModal = true;
        $this->searchResultados = '';
        $this->selectAll = false;
        $this->resultadosSeleccionados = [];
    }

    public function closeResultadosModal()
    {
        $this->showResultadosModal = false;
        $this->selectedCompetencia = null;
        $this->searchResultados = '';
        $this->selectAll = false;
        $this->resultadosSeleccionados = [];
    }

    public function refreshResultados()
    {
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Lista de resultados actualizada',
        ]);
    }
}
