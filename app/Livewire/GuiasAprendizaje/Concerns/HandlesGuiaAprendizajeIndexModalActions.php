<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\GuiasAprendizaje;

trait HandlesGuiaAprendizajeIndexModalActions
{
    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function openEditModal($guiaId)
    {
        $this->selectedGuia = GuiasAprendizaje::with(['resultadosAprendizaje'])->find($guiaId);
        $this->showEditModal = true;

        if ($this->showShowModal) {
            $this->showShowModal = false;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedGuia = null;
    }

    public function openDeleteModal($guiaId)
    {
        $this->selectedGuia = GuiasAprendizaje::find($guiaId);
        $this->showDeleteModal = true;

        if ($this->showShowModal) {
            $this->showShowModal = false;
        }
        if ($this->showEditModal) {
            $this->showEditModal = false;
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedGuia = null;
    }

    public function openShowModal($guiaId)
    {
        $this->selectedGuia = GuiasAprendizaje::with(['resultadosAprendizaje', 'actividades', 'userCreate', 'userEdit'])
            ->find($guiaId);
        $this->showShowModal = true;
    }

    public function closeShowModal()
    {
        $this->showShowModal = false;
        $this->selectedGuia = null;
    }

    public function handleCloseModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showShowModal = false;
        $this->showDeleteModal = false;
        $this->selectedGuia = null;
    }

    public function handleRefreshModal()
    {
        if ($this->selectedGuia) {
            $this->selectedGuia->refresh();
        }
    }
}
