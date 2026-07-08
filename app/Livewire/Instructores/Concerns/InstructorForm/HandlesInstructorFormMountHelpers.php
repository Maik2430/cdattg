<?php

namespace App\Livewire\Instructores\Concerns\InstructorForm;

trait HandlesInstructorFormMountHelpers
{
    public function mount($instructor = null, $isEdit = false): void
    {
        $this->isEdit = $isEdit;

        if ($instructor && $isEdit) {
            $this->instructor = $instructor;
            $this->loadInstructorData();
        }

        $this->cargarDatosSelects();
    }
}
