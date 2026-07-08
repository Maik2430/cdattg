<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

trait HandlesResultadoAprendizajeIndexFilterActions
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

    public function updatedCompetenciaFilter()
    {
        $this->resetPage();
        \Log::info('Competencia filter updated: '.$this->competenciaFilter);
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
        $this->competenciaFilter = '';
        $this->resetPage();
        \Log::info('Filters cleared');
    }

    public function gotoPage($page)
    {
        \Log::info('gotoPage called with page: '.$page);
        $this->page = $page;
    }

    public function formatearHoras($horas)
    {
        if ($horas == 0) {
            return '0';
        }

        return number_format($horas, 0, ',', '.');
    }
}
