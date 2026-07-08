<?php

namespace App\Livewire\Fichas\Concerns;

trait HandlesFichaIndexInstructoresSelectionActions
{
    public function updatedSelectAllInstructores()
    {
        if ($this->selectAllInstructores) {
            $this->selectedInstructores = $this->instructoresDisponibles->pluck('id')->toArray();
        } else {
            $this->selectedInstructores = [];
        }
    }

    public function updatedSelectAllInstructoresAsignados()
    {
        if ($this->selectAllInstructoresAsignados) {
            $this->selectedInstructoresAsignados = $this->selectedFichaInstructores->instructorFicha->pluck('id')->toArray();
        } else {
            $this->selectedInstructoresAsignados = [];
        }
    }

    public function updatedSearchInstructor()
    {
        // La búsqueda se filtra en la vista con el condicional
    }
}
