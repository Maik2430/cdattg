<?php

namespace App\Livewire\Fichas\Concerns;

trait HandlesFichaIndexAprendicesSelectionActions
{
    public function updatedSelectAllPersonas()
    {
        if ($this->selectAllPersonas) {
            $this->selectedPersonas = $this->personasDisponibles->pluck('id')->toArray();
        } else {
            $this->selectedPersonas = [];
        }
    }

    public function updatedSelectAllAprendicesAsignados()
    {
        if ($this->selectAllAprendicesAsignados) {
            $this->selectedAprendicesAsignados = $this->selectedFicha->aprendices->pluck('id')->toArray();
        } else {
            $this->selectedAprendicesAsignados = [];
        }
    }

    public function updatedSearchPersona()
    {
        // La búsqueda se filtra en la vista con el condicional
    }

    public function updatedSelectedPersonas()
    {
        // Depuración para ver qué se está seleccionando
        \Log::info('Personas seleccionadas actualizadas:', [
            'selectedPersonas' => $this->selectedPersonas,
            'count' => count($this->selectedPersonas),
            'personas_disponibles_count' => count($this->personasDisponibles),
        ]);
    }
}
