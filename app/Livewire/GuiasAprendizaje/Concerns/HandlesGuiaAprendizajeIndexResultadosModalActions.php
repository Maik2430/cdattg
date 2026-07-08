<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\GuiasAprendizaje;

trait HandlesGuiaAprendizajeIndexResultadosModalActions
{
    public function openGestionarResultados($guiaId)
    {
        $this->selectedGuia = GuiasAprendizaje::with(['resultadosAprendizaje'])->find($guiaId);

        if (! $this->selectedGuia) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Guía de aprendizaje no encontrada',
            ]);

            return;
        }

        $this->showGestionarResultadosModal = true;

        if ($this->showShowModal) {
            $this->showShowModal = false;
        }
    }

    public function closeGestionarResultadosModal()
    {
        $this->showGestionarResultadosModal = false;
        $this->selectedGuia = null;
        $this->searchResultados = '';
        $this->resultadosSeleccionados = [];
        $this->selectAll = false;
    }

    public function refreshResultados()
    {
        $this->searchResultados = '';
        $this->resultadosSeleccionados = [];
        $this->selectAll = false;
    }
}
