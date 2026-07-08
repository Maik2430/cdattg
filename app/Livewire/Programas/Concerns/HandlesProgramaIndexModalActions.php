<?php

namespace App\Livewire\Programas\Concerns;

use App\Models\ProgramaFormacion;

trait HandlesProgramaIndexModalActions
{
    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedPrograma = null;
    }

    public function closeCreateEditModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->selectedPrograma = null;
    }

    public function openEditModal($programaId)
    {
        $this->selectedPrograma = ProgramaFormacion::find($programaId);
        $this->showEditModal = true;

        if ($this->showShowModal) {
            $this->showShowModal = false;
        }
    }

    public function openShowModal($programaId)
    {
        $this->selectedPrograma = ProgramaFormacion::with(['redConocimiento', 'nivelFormacion', 'competencias'])
            ->find($programaId);
        $this->showShowModal = true;
    }

    public function handleCloseModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showShowModal = false;
        $this->showDeleteModal = false;
        $this->selectedPrograma = null;
    }

    public function closeShowModal()
    {
        $this->showShowModal = false;
        $this->selectedPrograma = null;
    }

    public function confirmDelete($programaId)
    {
        $this->selectedPrograma = ProgramaFormacion::with(['redConocimiento', 'competencias', 'fichasCaracterizacion'])
            ->find($programaId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedPrograma = null;
    }
}
