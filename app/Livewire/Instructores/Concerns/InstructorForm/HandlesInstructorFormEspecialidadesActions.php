<?php

namespace App\Livewire\Instructores\Concerns\InstructorForm;

trait HandlesInstructorFormEspecialidadesActions
{
    public function updatedEspecialidades(): void
    {
        if (! is_array($this->especialidades)) {
            $this->especialidades = [];
        }

        if (! isset($this->especialidades['principal'])) {
            $this->especialidades['principal'] = null;
        }

        if (! isset($this->especialidades['secundarias'])) {
            $this->especialidades['secundarias'] = [];
        }
    }

    public function handleCloseModal(): void
    {
        // Este método es llamado cuando el modal se cierra desde el componente padre
    }
}
