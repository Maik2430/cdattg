<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

trait HandlesGuiaAprendizajeGestionarResultadosModalActions
{
    public function closeModal()
    {
        $this->dispatch('closeGestionarResultadosModal');
    }
}
