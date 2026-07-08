<?php

namespace App\Livewire\Fichas\Concerns;

trait HandlesFichaIndexFilterActions
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
        $this->search = '';
        $this->programaFilter = '';
        $this->regionalFilter = '';
        $this->sedeFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedProgramaFilter()
    {
        $this->resetPage();
    }

    public function updatedRegionalFilter()
    {
        $this->resetPage();
    }

    public function updatedSedeFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}
