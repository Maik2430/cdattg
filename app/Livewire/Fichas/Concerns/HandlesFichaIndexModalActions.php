<?php

namespace App\Livewire\Fichas\Concerns;

use App\Models\FichaCaracterizacion;

trait HandlesFichaIndexModalActions
{
    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->showEditModal = false;
        $this->selectedFicha = null;
    }

    public function openEditModal($fichaId)
    {
        $this->selectedFicha = FichaCaracterizacion::with([
            'programaFormacion',
            'sede',
            'instructor.persona',
            'ambiente',
        ])
            ->withCount('aprendices')
            ->find($fichaId);

        if ($this->selectedFicha) {
            $this->showEditModal = true;
            $this->showCreateModal = false;
        }
    }

    public function openShowModal($fichaId)
    {
        $this->selectedFicha = FichaCaracterizacion::with([
            'programaFormacion.redConocimiento.regional',
            'sede.regional',
            'instructor.persona',
            'ambiente',
            'aprendices.persona',
        ])
            ->withCount('aprendices')
            ->find($fichaId);

        if ($this->selectedFicha) {
            $this->showShowModal = true;
        }
    }

    public function openDeleteModal($fichaId)
    {
        $this->selectedFicha = FichaCaracterizacion::withCount('aprendices')->find($fichaId);

        if ($this->selectedFicha) {
            $this->showDeleteModal = true;
        }
    }

    public function closeCreateEditModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->selectedFicha = null;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedFicha = null;
    }

    public function handleCloseModal()
    {
        $this->closeCreateEditModals();
        $this->closeDeleteModal();
        $this->showShowModal = false;
    }
}
