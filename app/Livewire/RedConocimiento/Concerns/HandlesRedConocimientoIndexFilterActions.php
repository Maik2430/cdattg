<?php

namespace App\Livewire\RedConocimiento\Concerns;

use App\Models\Regional;

trait HandlesRedConocimientoIndexFilterActions
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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
        \Log::info('Status filter updated: '.$this->statusFilter);
    }

    public function updatedRegionalFilter()
    {
        $this->resetPage();
        \Log::info('Regional filter updated: '.$this->regionalFilter);
    }

    public function updatedPerPage()
    {
        $this->resetPage();
        \Log::info('PerPage updated: '.$this->perPage);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->regionalFilter = '';
        $this->resetPage();
        \Log::info('Filters cleared');
    }

    public function getRegionalesProperty()
    {
        return Regional::where('status', 1)->get();
    }
}
