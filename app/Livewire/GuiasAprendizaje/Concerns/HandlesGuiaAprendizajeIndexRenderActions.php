<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesGuiaAprendizajeIndexRenderActions
{
    use HandlesGuiaAprendizajeIndexRenderQueryHelpers;

    public function render()
    {
        $query = $this->buildGuiasQuery();

        \Log::info('GuiaAprendizajeIndex render - Filtros:', [
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'resultadoFilter' => $this->resultadoFilter,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'perPage' => $this->perPage,
            'page' => $this->page ?? 1,
        ]);

        $guias = $query->paginate($this->perPage);

        \Log::info('GuiaAprendizajeIndex render - Guías:', [
            'total' => $guias->total(),
            'count' => $guias->count(),
            'currentPage' => $guias->currentPage(),
            'lastPage' => $guias->lastPage(),
            'perPage' => $guias->perPage(),
            'hasMorePages' => $guias->hasMorePages(),
        ]);

        $resultados = ResultadosAprendizaje::orderBy('nombre')->get();

        return view('livewire.guias-aprendizaje.guia-aprendizaje-index', compact('guias', 'resultados'));
    }
}
