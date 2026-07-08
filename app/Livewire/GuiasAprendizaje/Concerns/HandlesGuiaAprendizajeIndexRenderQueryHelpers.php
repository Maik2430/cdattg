<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\GuiasAprendizaje;
use Illuminate\Database\Eloquent\Builder;

trait HandlesGuiaAprendizajeIndexRenderQueryHelpers
{
    private function buildGuiasQuery(): Builder
    {
        $query = GuiasAprendizaje::with(['resultadosAprendizaje', 'actividades', 'userCreate', 'userEdit']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('codigo', 'like', '%'.$this->search.'%')
                    ->orWhere('nombre', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter === '1');
        }

        if ($this->resultadoFilter !== '') {
            $query->whereHas('resultadosAprendizaje', function ($q) {
                $q->where('resultados_aprendizaje.id', $this->resultadoFilter);
            });
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }
}
