<?php

namespace App\Livewire\Instructores\Concerns;

trait HandlesInstructorIndexModalCloseActions
{
    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->selectedInstructor = null;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedInstructor = null;
    }

    public function closeShowModal()
    {
        $this->showShowModal = false;
        $this->selectedInstructor = null;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedInstructor = null;
    }

    public function closeEspecialidadesModal()
    {
        $this->showEspecialidadesModal = false;
        $this->selectedInstructor = null;
    }

    public function closeFichasModal()
    {
        $this->showFichasModal = false;
        $this->selectedInstructor = null;
    }

    public function handleCloseModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showShowModal = false;
        $this->showDeleteModal = false;
        $this->showEspecialidadesModal = false;
        $this->showFichasModal = false;
        $this->selectedInstructor = null;
    }
}
