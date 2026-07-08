<?php

namespace App\Livewire\Concerns;

trait MapsLivewireRefreshListeners
{
    /**
     * @param  list<string>  $events
     * @return array<string, string>
     */
    protected function refreshListeners(array $events): array
    {
        return array_fill_keys($events, '$refresh');
    }
}
