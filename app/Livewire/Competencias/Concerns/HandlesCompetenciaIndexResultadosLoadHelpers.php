<?php

namespace App\Livewire\Competencias\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesCompetenciaIndexResultadosLoadHelpers
{
    public function getResultadosDisponibles()
    {
        $query = ResultadosAprendizaje::activos()->ordenadoPorCodigo();

        if ($this->searchResultados) {
            $query->where(function ($q) {
                $q->where('codigo', 'like', '%'.$this->searchResultados.'%')
                    ->orWhere('nombre', 'like', '%'.$this->searchResultados.'%');
            });
        }

        if ($this->selectedCompetencia) {
            $asignadosIds = $this->selectedCompetencia->resultadosAprendizaje->pluck('id')->toArray();
            if (! empty($asignadosIds)) {
                $query->whereNotIn('id', $asignadosIds);
            }
        }

        return $query->get();
    }
}
