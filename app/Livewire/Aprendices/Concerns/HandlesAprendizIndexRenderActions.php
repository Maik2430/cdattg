<?php

namespace App\Livewire\Aprendices\Concerns;

trait HandlesAprendizIndexRenderActions
{
    use HandlesAprendizIndexRenderQueryHelpers;

    public function render()
    {
        $query = $this->buildAprendicesQuery();
        $aprendices = $query->paginate($this->perPage);

        \Log::info('Aprendices query debug', [
            'perPage' => $this->perPage,
            'total' => $aprendices->total(),
            'currentPage' => $aprendices->currentPage(),
            'count' => $aprendices->count(),
            'hasItems' => $aprendices->count() > 0,
        ]);

        $filterOptions = $this->loadAprendizFilterOptions();

        return view('livewire.aprendices.aprendiz-index', [
            'aprendices' => $aprendices,
            'fichas' => $filterOptions['fichas'],
            'programas' => $filterOptions['programas'],
            'regionales' => $filterOptions['regionales'],
        ]);
    }
}
