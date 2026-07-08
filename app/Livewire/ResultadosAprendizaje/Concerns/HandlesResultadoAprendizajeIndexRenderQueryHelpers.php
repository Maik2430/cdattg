<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;
use Illuminate\Database\Eloquent\Builder;

trait HandlesResultadoAprendizajeIndexRenderQueryHelpers
{
    private function buildResultadosAprendizajeQuery(): Builder
    {
        $query = ResultadosAprendizaje::with(['competencias', 'guiasAprendizaje', 'userCreate', 'userEdit']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('codigo', 'like', '%'.$this->search.'%')
                    ->orWhere('nombre', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter === '1');
        }

        if ($this->competenciaFilter !== '') {
            $query->whereHas('competencias', function ($q) {
                $q->where('competencias.id', $this->competenciaFilter);
            });
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }
}
