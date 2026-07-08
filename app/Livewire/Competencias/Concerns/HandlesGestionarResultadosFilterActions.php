<?php

namespace App\Livewire\Competencias\Concerns;

trait HandlesGestionarResultadosFilterActions
{
    public function updatingSearchAsignados()
    {
        $this->resetPage();
    }

    public function updatingSearchDisponibles() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function formatearHoras($horas)
    {
        if ($horas == 0) {
            return '0';
        }

        return number_format($horas, 0, ',', '.');
    }
}
