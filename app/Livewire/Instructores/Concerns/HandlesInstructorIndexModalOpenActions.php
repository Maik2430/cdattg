<?php

namespace App\Livewire\Instructores\Concerns;

use App\Models\Instructor;

trait HandlesInstructorIndexModalOpenActions
{
    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->showEditModal = false;
        $this->showShowModal = false;
        $this->showDeleteModal = false;
    }

    public function openEditModal($instructorId)
    {
        $this->selectedInstructor = Instructor::with([
            'persona',
            'regional',
            'centroFormacion',
            'tipoVinculacion',
            'nivelAcademico',
        ])->find($instructorId);

        if (! $this->selectedInstructor) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Instructor no encontrado',
            ]);

            return;
        }

        $this->showEditModal = true;
        $this->showCreateModal = false;
        $this->showShowModal = false;
        $this->showDeleteModal = false;
    }

    public function openShowModal($instructorId)
    {
        $this->selectedInstructor = Instructor::with([
            'persona',
            'regional',
            'centroFormacion',
            'tipoVinculacion',
            'nivelAcademico',
            'userCreated',
            'userEdited',
        ])->find($instructorId);

        if (! $this->selectedInstructor) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Instructor no encontrado',
            ]);

            return;
        }

        $this->showShowModal = true;
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
    }

    public function openDeleteModal($instructorId)
    {
        $this->selectedInstructor = Instructor::find($instructorId);

        if (! $this->selectedInstructor) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Instructor no encontrado',
            ]);

            return;
        }

        $this->showDeleteModal = true;
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showShowModal = false;
    }
}
