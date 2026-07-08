<?php

namespace App\Livewire\Aprendices\Concerns;

trait HandlesAprendizIndexMountHelpers
{
    public function mount()
    {
        $this->perPage = 15;
    }
}
