<?php

namespace App\Livewire\Competencias\Concerns;

trait HandlesCompetenciaIndexMountHelpers
{
    public function mount()
    {
        // No establecer perPage aquí, ya que se establece en el queryString
        // $this->perPage = 15;  // ← Esto causa el problema
        \Log::info('CompetenciaIndex mounted');
    }
}
