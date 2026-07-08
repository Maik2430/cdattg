<?php

namespace App\Livewire\Aprendices\Concerns;

trait HandlesAprendizIndexFilterActions
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
        $this->fichaFilter = '';
        $this->programaFilter = '';
        $this->regionalFilter = '';
        $this->statusFilter = '';
        $this->perPage = 15;
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}
