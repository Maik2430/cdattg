<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

trait HandlesGuiaAprendizajeIndexFilterActions
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

    public function updatedSearch()
    {
        $this->resetPage();
        \Log::info('Search updated: '.$this->search);
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
        \Log::info('Status filter updated: '.$this->statusFilter);
    }

    public function updatedResultadoFilter()
    {
        $this->resetPage();
        \Log::info('Resultado filter updated: '.$this->resultadoFilter);
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
        $this->resultadoFilter = '';
        $this->resetPage();
        \Log::info('Filters cleared');
    }

    public function gotoPage($page)
    {
        \Log::info('gotoPage called with page: '.$page);
        $this->page = $page;
    }
}
