<?php

namespace App\Livewire\Programas\Concerns;

trait HandlesProgramaIndexRenderActions
{
    use HandlesProgramaIndexRenderQueryHelpers;

    public function render()
    {
        $programas = $this->buildProgramasQuery()->paginate($this->perPage);
        $filterData = $this->getProgramaIndexFilterData();

        return view('livewire.programas.programa-index', array_merge(
            compact('programas'),
            $filterData
        ));
    }
}
