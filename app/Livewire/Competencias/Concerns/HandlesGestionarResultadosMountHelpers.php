<?php

namespace App\Livewire\Competencias\Concerns;

use App\Models\Competencia;

trait HandlesGestionarResultadosMountHelpers
{
    public function mount(Competencia $competencia)
    {
        $this->competencia = $competencia;
        \Log::info('GestionarResultados mounted for competencia: '.$competencia->id);
    }
}
