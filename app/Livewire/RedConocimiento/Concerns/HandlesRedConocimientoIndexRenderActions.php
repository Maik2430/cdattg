<?php

namespace App\Livewire\RedConocimiento\Concerns;

trait HandlesRedConocimientoIndexRenderActions
{
    use HandlesRedConocimientoIndexRenderQueryHelpers;

    public function render()
    {
        $query = $this->buildRedesConocimientoQuery();

        \Log::info('RedConocimientoIndex render - Filtros:', [
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'regionalFilter' => $this->regionalFilter,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'perPage' => $this->perPage,
        ]);

        $redes = $query->paginate($this->perPage);

        \Log::info('RedConocimientoIndex render - Resultados:', [
            'total' => $redes->total(),
            'count' => $redes->count(),
        ]);

        return view('livewire.red-conocimiento.red-conocimiento-index', compact('redes'));
    }
}
