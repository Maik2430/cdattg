<?php

namespace App\Livewire\Programas\Concerns;

use App\Models\Parametro;
use App\Models\ProgramaFormacion;
use App\Models\RedConocimiento;
use Illuminate\Database\Eloquent\Builder;

trait HandlesProgramaIndexRenderQueryHelpers
{
    private function buildProgramasQuery(): Builder
    {
        return ProgramaFormacion::with(['redConocimiento', 'nivelFormacion'])
            ->when($this->search, function ($query) {
                $query->where('codigo', 'like', '%'.$this->search.'%')
                    ->orWhere('nombre', 'like', '%'.$this->search.'%');
            })
            ->when($this->redConocimientoFilter, function ($query) {
                $query->where('red_conocimiento_id', $this->redConocimientoFilter);
            })
            ->when($this->nivelFilter, function ($query) {
                $query->where('nivel_formacion_id', $this->nivelFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    private function getProgramaIndexFilterData(): array
    {
        $redesConocimiento = RedConocimiento::orderBy('nombre')->get();

        $nivelesFormacion = Parametro::whereIn('name', ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'])
            ->orderBy('name')
            ->get();

        return compact('redesConocimiento', 'nivelesFormacion');
    }
}
