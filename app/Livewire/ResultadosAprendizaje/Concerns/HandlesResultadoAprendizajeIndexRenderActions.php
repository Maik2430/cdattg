<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\Competencia;

trait HandlesResultadoAprendizajeIndexRenderActions
{
    use HandlesResultadoAprendizajeIndexRenderQueryHelpers;

    public function render()
    {
        $query = $this->buildResultadosAprendizajeQuery();

        \Log::info('ResultadoAprendizajeIndex render - Filtros:', [
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'competenciaFilter' => $this->competenciaFilter,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'perPage' => $this->perPage,
            'page' => $this->page ?? 1,
        ]);

        $resultados = $query->paginate($this->perPage);

        \Log::info('ResultadoAprendizajeIndex render - Resultados:', [
            'total' => $resultados->total(),
            'count' => $resultados->count(),
            'currentPage' => $resultados->currentPage(),
            'lastPage' => $resultados->lastPage(),
            'perPage' => $resultados->perPage(),
            'hasMorePages' => $resultados->hasMorePages(),
        ]);

        $competencias = Competencia::orderBy('nombre')->get();

        return view('livewire.resultados-aprendizaje.resultado-aprendizaje-index', compact('resultados', 'competencias'));
    }
}
