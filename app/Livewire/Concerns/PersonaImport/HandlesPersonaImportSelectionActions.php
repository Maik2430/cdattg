<?php

namespace App\Livewire\Concerns\PersonaImport;

trait HandlesPersonaImportSelectionActions
{
    public function updatedImportacionSeleccionada($value): void
    {
        if ($value) {
            $this->seleccionarImportacion($value);
        }
    }

    public function seleccionarImportacion($importId): void
    {
        if (! $importId) {
            return;
        }

        $this->importacionId = $importId;
        $this->importacionSeleccionada = $importId;
        $this->mostrarProgreso = true;
        $this->actualizarProgreso();
    }
}
