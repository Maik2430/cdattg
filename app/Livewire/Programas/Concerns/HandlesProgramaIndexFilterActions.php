<?php

namespace App\Livewire\Programas\Concerns;

trait HandlesProgramaIndexFilterActions
{
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'redConocimientoFilter', 'nivelFilter', 'statusFilter']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
