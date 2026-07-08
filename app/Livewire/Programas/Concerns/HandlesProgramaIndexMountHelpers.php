<?php

namespace App\Livewire\Programas\Concerns;

trait HandlesProgramaIndexMountHelpers
{
    public function mount()
    {
        $this->perPage = 15;
    }
}
