<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\Competencia;

trait HandlesResultadoAprendizajeIndexCompetenciasLoadHelpers
{
    public function getCompetenciasDisponibles()
    {
        $query = Competencia::query();

        if ($this->searchCompetencias) {
            $query->where(function ($q) {
                $q->where('codigo', 'like', '%'.$this->searchCompetencias.'%')
                    ->orWhere('nombre', 'like', '%'.$this->searchCompetencias.'%');
            });
        }

        if ($this->selectedResultado) {
            $competenciasAsignadas = $this->selectedResultado->competencias->pluck('id')->toArray();
            $query->whereNotIn('id', $competenciasAsignadas);
        }

        return $query->orderBy('codigo')->get();
    }
}
