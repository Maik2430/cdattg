<?php

namespace App\Livewire\Aprendices\Concerns;

use App\Models\Aprendiz;
use App\Models\FichaCaracterizacion;
use App\Models\Persona;
use App\Models\ProgramaFormacion;
use App\Models\Regional;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HandlesAprendizIndexRenderQueryHelpers
{
    private function buildAprendicesQuery(): Builder
    {
        $query = Aprendiz::with([
            'persona',
            'fichaCaracterizacion.programaFormacion',
            'fichaCaracterizacion.sede.regional',
        ]);

        if (Auth::user()->hasRole('instructor')) {
            $query->whereHas('fichaCaracterizacion', function ($q) {
                $q->where('instructor_id', Auth::user()->instructor?->id);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('persona', function ($subQuery) {
                    $subQuery->where('primer_nombre', 'like', '%'.$this->search.'%')
                        ->orWhere('segundo_nombre', 'like', '%'.$this->search.'%')
                        ->orWhere('primer_apellido', 'like', '%'.$this->search.'%')
                        ->orWhere('segundo_apellido', 'like', '%'.$this->search.'%')
                        ->orWhere('numero_documento', 'like', '%'.$this->search.'%');
                })
                    ->orWhereHas('fichaCaracterizacion', function ($subQuery) {
                        $subQuery->where('ficha', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->fichaFilter) {
            $query->where('ficha_caracterizacion_id', $this->fichaFilter);
        }

        if ($this->programaFilter) {
            $query->whereHas('fichaCaracterizacion', function ($q) {
                $q->where('programa_formacion_id', $this->programaFilter);
            });
        }

        if ($this->regionalFilter) {
            $query->whereHas('fichaCaracterizacion.sede', function ($q) {
                $q->where('regional_id', $this->regionalFilter);
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('estado', $this->statusFilter);
        }

        return $this->applyAprendizSorting($query);
    }

    private function applyAprendizSorting(Builder $query): Builder
    {
        if ($this->sortField === 'nombre') {
            return $query->orderBy(
                Persona::selectRaw("CONCAT(primer_nombre, ' ', primer_apellido)")
                    ->whereColumn('personas.id', 'aprendices.persona_id'),
                $this->sortDirection
            );
        }

        if ($this->sortField === 'ficha') {
            return $query->orderBy(
                FichaCaracterizacion::select('ficha')
                    ->whereColumn('fichas_caracterizacion.id', 'aprendices.ficha_caracterizacion_id'),
                $this->sortDirection
            );
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    /**
     * @return array{fichas: \Illuminate\Support\Collection, programas: \Illuminate\Support\Collection, regionales: \Illuminate\Support\Collection}
     */
    private function loadAprendizFilterOptions(): array
    {
        $fichas = FichaCaracterizacion::where('status', true)
            ->with('programaFormacion')
            ->orderBy('ficha')
            ->get();

        $programas = ProgramaFormacion::where('status', true)
            ->orderBy('nombre')
            ->get();

        $regionales = Regional::where('status', true)
            ->orderBy('nombre')
            ->get();

        return compact('fichas', 'programas', 'regionales');
    }
}
