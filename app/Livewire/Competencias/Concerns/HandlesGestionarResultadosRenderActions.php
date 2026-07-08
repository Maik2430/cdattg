<?php

namespace App\Livewire\Competencias\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesGestionarResultadosRenderActions
{
    public function render()
    {
        $resultadosAsignadosQuery = $this->competencia->resultadosAprendizaje();

        if ($this->searchAsignados) {
            $resultadosAsignadosQuery->where(function ($query) {
                $query->where('codigo', 'like', '%'.$this->searchAsignados.'%')
                    ->orWhere('nombre', 'like', '%'.$this->searchAsignados.'%');
            });
        }

        $resultadosAsignados = $resultadosAsignadosQuery->paginate($this->perPage);

        $resultadosDisponiblesQuery = ResultadosAprendizaje::where('status', 1)
            ->whereNotIn('resultados_aprendizajes.id', $this->competencia->resultadosAprendizaje()->pluck('resultados_aprendizajes.id'));

        if ($this->searchDisponibles) {
            $resultadosDisponiblesQuery->where(function ($query) {
                $query->where('codigo', 'like', '%'.$this->searchDisponibles.'%')
                    ->orWhere('nombre', 'like', '%'.$this->searchDisponibles.'%');
            });
        }

        $resultadosDisponibles = $resultadosDisponiblesQuery->get();

        $duracionTotal = \DB::table('resultados_aprendizaje_competencia')
            ->where('competencia_id', $this->competencia->id)
            ->sum('duracion');

        $totalAsignados = $this->competencia->resultadosAprendizaje()->count();
        $totalDisponibles = $resultadosDisponibles->count();

        return view('livewire.competencias.gestionar-resultados', compact(
            'resultadosAsignados',
            'resultadosDisponibles',
            'totalAsignados',
            'totalDisponibles',
            'duracionTotal'
        ));
    }
}
