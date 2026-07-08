<?php

namespace App\Livewire\Competencias\Concerns;

trait HandlesCompetenciaIndexFilterActions
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

    public function updatedVigenciaFilter()
    {
        $this->resetPage();
        \Log::info('Vigencia filter updated: '.$this->vigenciaFilter);
    }

    public function updatedPerPage()
    {
        $this->resetPage();
        \Log::info('PerPage updated: '.$this->perPage);
        \Log::info('After resetPage - Current page: '.$this->page);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->vigenciaFilter = '';
        $this->resetPage();
        \Log::info('Filters cleared');
    }

    public function gotoPage($page)
    {
        \Log::info('gotoPage called with page: '.$page);
        \Log::info('Before gotoPage - Current page: '.$this->page);

        $this->page = $page;

        \Log::info('After gotoPage - New page: '.$this->page);
        \Log::info('Going to page: '.$page);
    }

    public function setPage($page)
    {
        \Log::info('setPage called with: '.$page);
        $this->page = $page;
    }

    public function refreshPagination()
    {
        \Log::info('Refreshing pagination');
        $this->dispatch('refreshPagination');
    }

    public function debugPagination()
    {
        \Log::info('=== DEBUG PAGINATION ===');
        \Log::info('Current page: '.$this->page);
        \Log::info('Per page: '.$this->perPage);

        $this->dispatch('debugPagination');
    }
}
