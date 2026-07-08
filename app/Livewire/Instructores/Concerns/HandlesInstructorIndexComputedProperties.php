<?php

namespace App\Livewire\Instructores\Concerns;

use App\Models\Persona;

trait HandlesInstructorIndexComputedProperties
{
    public function getPersonasDisponiblesProperty()
    {
        return Persona::query()
            ->whereDoesntHave('instructor')
            ->orderBy('primer_nombre')
            ->orderBy('primer_apellido')
            ->get();
    }
}
