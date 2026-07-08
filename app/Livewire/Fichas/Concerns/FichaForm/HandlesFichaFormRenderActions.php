<?php

namespace App\Livewire\Fichas\Concerns\FichaForm;

use Livewire\Attributes\On;

trait HandlesFichaFormRenderActions
{
    public function render()
    {
        return view('livewire.fichas.ficha-form');
    }

    public function closeModal(): void
    {
        $this->dispatch('closeModal');
    }

    #[On('showNotification')]
    public function showNotification($type, $message): void
    {
        // Este método es para el sistema de notificaciones
        // El JavaScript manejará la visualización
    }
}
