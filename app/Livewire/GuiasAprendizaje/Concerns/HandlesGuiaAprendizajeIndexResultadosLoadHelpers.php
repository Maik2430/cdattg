<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesGuiaAprendizajeIndexResultadosLoadHelpers
{
    public function getResultadosDisponibles()
    {
        $query = ResultadosAprendizaje::query();

        if ($this->searchResultados) {
            $query->where(function ($q) {
                $q->where('codigo', 'like', '%'.$this->searchResultados.'%')
                    ->orWhere('nombre', 'like', '%'.$this->searchResultados.'%');
            });
        }

        if ($this->selectedGuia) {
            $resultadosAsignados = $this->selectedGuia->resultadosAprendizaje->pluck('id')->toArray();
            $query->whereNotIn('id', $resultadosAsignados);
        }

        return $query->orderBy('codigo')->get();
    }
}
