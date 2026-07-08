<?php

namespace App\Livewire\Competencias\Concerns;

use App\Models\Competencia;
use Illuminate\Database\Eloquent\Builder;

trait HandlesCompetenciaIndexRenderQueryHelpers
{
    private function buildCompetenciasQuery(): Builder
    {
        $query = Competencia::with(['programasFormacion']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('codigo', 'like', '%'.$this->search.'%')
                    ->orWhere('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('descripcion', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter === '1');
        }

        if ($this->vigenciaFilter !== '') {
            if ($this->vigenciaFilter === 'vigentes') {
                $query->where('fecha_inicio', '<=', now())
                    ->where('fecha_fin', '>=', now());
            } elseif ($this->vigenciaFilter === 'no_vigentes') {
                $query->where(function ($q) {
                    $q->where('fecha_inicio', '>', now())
                        ->orWhere('fecha_fin', '<', now())
                        ->orWhereNull('fecha_inicio')
                        ->orWhereNull('fecha_fin');
                });
            }
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }
}
