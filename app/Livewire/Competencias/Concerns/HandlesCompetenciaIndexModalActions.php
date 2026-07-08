<?php

namespace App\Livewire\Competencias\Concerns;

use App\Models\Competencia;

trait HandlesCompetenciaIndexModalActions
{
    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function openEditModal($competenciaId)
    {
        $this->selectedCompetencia = Competencia::find($competenciaId);
        $this->showEditModal = true;

        if ($this->showShowModal) {
            $this->showShowModal = false;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedCompetencia = null;
    }

    public function openShowModal($competenciaId)
    {
        $this->selectedCompetencia = Competencia::with(['programasFormacion', 'resultadosCompetencia'])
            ->find($competenciaId);
        $this->showShowModal = true;
    }

    public function closeShowModal()
    {
        $this->showShowModal = false;
        $this->selectedCompetencia = null;
    }

    public function handleCloseModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showShowModal = false;
        $this->showDeleteModal = false;
        $this->selectedCompetencia = null;
    }

    public function handleRefreshModal()
    {
        if ($this->selectedCompetencia) {
            $this->selectedCompetencia->refresh();
        }
    }

    public function confirmDelete($competenciaId)
    {
        $this->selectedCompetencia = Competencia::with(['programasFormacion'])->find($competenciaId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedCompetencia = null;
    }
}
