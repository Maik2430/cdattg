<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesResultadoAprendizajeIndexModalActions
{
    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function openEditModal($resultadoId)
    {
        $this->selectedResultado = ResultadosAprendizaje::with(['competencias'])->find($resultadoId);
        $this->showEditModal = true;

        if ($this->showShowModal) {
            $this->showShowModal = false;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedResultado = null;
    }

    public function openShowModal($resultadoId)
    {
        $this->selectedResultado = ResultadosAprendizaje::with([
            'competencias',
            'guiasAprendizaje',
            'userCreate',
            'userEdit',
        ])->find($resultadoId);
        $this->showShowModal = true;
    }

    public function closeShowModal()
    {
        $this->showShowModal = false;
        $this->selectedResultado = null;
    }

    public function handleCloseModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showShowModal = false;
        $this->showDeleteModal = false;
        $this->showCompetenciasModal = false;
        $this->selectedResultado = null;
    }

    public function handleRefreshModal()
    {
        if ($this->selectedResultado) {
            $this->selectedResultado->refresh();
        }
    }

    public function confirmDelete($resultadoId)
    {
        $this->selectedResultado = ResultadosAprendizaje::with(['guiasAprendizaje', 'competencias'])
            ->find($resultadoId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedResultado = null;
    }
}
