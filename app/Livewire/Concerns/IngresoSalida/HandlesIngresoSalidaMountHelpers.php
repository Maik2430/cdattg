<?php

namespace App\Livewire\Concerns\IngresoSalida;

use App\Models\Sede;

trait HandlesIngresoSalidaMountHelpers
{
    public function mount(): void
    {
        $sedesActivas = Sede::where('status', 1)->get();
        if ($sedesActivas->count() === 1) {
            $this->sedeId = $sedesActivas->first()->id;
            $this->sedeSeleccionada = $sedesActivas->first();
        }
    }
}
