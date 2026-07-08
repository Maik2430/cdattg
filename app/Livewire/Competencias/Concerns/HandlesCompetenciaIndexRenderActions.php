<?php

namespace App\Livewire\Competencias\Concerns;

trait HandlesCompetenciaIndexRenderActions
{
    use HandlesCompetenciaIndexRenderQueryHelpers;

    public function render()
    {
        $query = $this->buildCompetenciasQuery();

        \Log::info('CompetenciaIndex render - Filtros:', [
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'vigenciaFilter' => $this->vigenciaFilter,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'perPage' => $this->perPage,
            'page' => $this->page ?? 1,
        ]);

        \Log::info('Query SQL before paginate: '.$query->toSql());
        \Log::info('Query bindings: '.json_encode($query->getBindings()));

        $competencias = $query->paginate($this->perPage);

        \Log::info('CompetenciaIndex render - Resultados:', [
            'total' => $competencias->total(),
            'count' => $competencias->count(),
            'currentPage' => $competencias->currentPage(),
            'lastPage' => $competencias->lastPage(),
            'perPage' => $competencias->perPage(),
            'hasMorePages' => $competencias->hasMorePages(),
        ]);

        if ($competencias->currentPage() == 2) {
            \Log::info('Página 2 específica - Items encontrados: '.$competencias->count());
            $itemsIds = collect($competencias->items())->pluck('id')->toArray();
            \Log::info('Página 2 específica - Items IDs: '.json_encode($itemsIds));
            \Log::info('Página 2 específica - LastPage: '.$competencias->lastPage());
            \Log::info('Página 2 específica - Total: '.$competencias->total());
            \Log::info('Página 2 específica - PerPage: '.$competencias->perPage());
        }

        \Log::info('Paginación Debug:', [
            'currentPage' => $competencias->currentPage(),
            'lastPage' => $competencias->lastPage(),
            'total' => $competencias->total(),
            'perPage' => $competencias->perPage(),
            'hasMorePages' => $competencias->hasMorePages(),
            'count' => $competencias->count(),
            'firstItem' => $competencias->firstItem(),
            'lastItem' => $competencias->lastItem(),
        ]);

        return view('livewire.competencias.competencia-index', compact('competencias'));
    }
}
