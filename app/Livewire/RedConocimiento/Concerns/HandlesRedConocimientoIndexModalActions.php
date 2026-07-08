<?php

namespace App\Livewire\RedConocimiento\Concerns;

use App\Models\RedConocimiento;

trait HandlesRedConocimientoIndexModalActions
{
    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->selectedRed = null;
    }

    public function openEditModal($redId)
    {
        $this->selectedRed = RedConocimiento::find($redId);
        $this->showEditModal = true;

        if ($this->showShowModal) {
            $this->showShowModal = false;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedRed = null;
    }

    public function openShowModal($redId)
    {
        $this->selectedRed = RedConocimiento::with(['regional', 'programasFormacion'])->find($redId);
        $this->showShowModal = true;
    }

    public function closeShowModal()
    {
        $this->showShowModal = false;
        $this->selectedRed = null;
    }

    public function handleCloseModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showShowModal = false;
        $this->showDeleteModal = false;
        $this->selectedRed = null;
    }

    public function handleRefreshModal()
    {
        if ($this->selectedRed) {
            $this->selectedRed->refresh();
        }
    }

    public function confirmDelete($redId)
    {
        $this->selectedRed = RedConocimiento::with(['programasFormacion'])->find($redId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedRed = null;
    }
}
