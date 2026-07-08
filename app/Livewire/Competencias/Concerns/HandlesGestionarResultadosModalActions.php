<?php

namespace App\Livewire\Competencias\Concerns;

use App\Livewire\Concerns\HandlesLivewireNotifyListener;
use App\Models\ResultadosAprendizaje;

trait HandlesGestionarResultadosModalActions
{
    use HandlesLivewireNotifyListener;

    public function openAsociarModal(): void
    {
        $this->showAsociarModal = true;
        $this->selectedResultados = [];
    }

    public function closeAsociarModal(): void
    {
        $this->showAsociarModal = false;
        $this->selectedResultados = [];
    }

    public function openDesasociarModal(int|string $resultadoId): void
    {
        $this->selectedResultado = ResultadosAprendizaje::find($resultadoId);
        $this->showDesasociarModal = true;
    }

    public function closeDesasociarModal(): void
    {
        $this->showDesasociarModal = false;
        $this->selectedResultado = null;
    }

    public function handleCloseModal(): void
    {
        $this->showAsociarModal = false;
        $this->showDesasociarModal = false;
        $this->selectedResultados = [];
        $this->selectedResultado = null;
    }
}
