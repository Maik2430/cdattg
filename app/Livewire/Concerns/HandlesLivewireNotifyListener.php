<?php

namespace App\Livewire\Concerns;

trait HandlesLivewireNotifyListener
{
    /**
     * Listener de eventos `notify`. La visualización la maneja el frontend.
     *
     * @param  array<string, mixed>|mixed  $data
     */
    public function showNotification(mixed $data = null): void
    {
        // Sin implementación en servidor: el JavaScript escucha el evento Livewire.
    }
}
