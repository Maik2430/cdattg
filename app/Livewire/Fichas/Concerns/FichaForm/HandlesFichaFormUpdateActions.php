<?php

namespace App\Livewire\Fichas\Concerns\FichaForm;

trait HandlesFichaFormUpdateActions
{
    public function updatedProgramaFormacionId(): void
    {
        // Cuando se cambia el programa, se puede filtrar instructores por especialidad
        // si se requiere en el futuro
    }

    public function updatedSedeId(): void
    {
        // Cuando se cambia la sede, se puede filtrar ambientes por sede
        // si se requiere en el futuro
    }
}
