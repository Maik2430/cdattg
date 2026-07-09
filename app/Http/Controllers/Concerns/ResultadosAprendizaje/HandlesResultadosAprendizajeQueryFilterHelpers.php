<?php

namespace App\Http\Controllers\Concerns\ResultadosAprendizaje;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HandlesResultadosAprendizajeQueryFilterHelpers
{
    /**
     * @param  Builder<\App\Models\ResultadosAprendizaje>  $query
     */
    protected function applyResultadosAprendizajeListFilters(Builder $query, Request $request, bool $ajaxSearch = false): void
    {
        $searchField = $ajaxSearch ? 'q' : 'search';

        if ($request->filled($searchField)) {
            $searchTerm = $request->input($searchField);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('codigo', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('nombre', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('codigo')) {
            $query->where('codigo', 'LIKE', "%{$request->codigo}%");
        }

        if ($request->filled('nombre')) {
            $query->where('nombre', 'LIKE', "%{$request->nombre}%");
        }

        if ($request->filled('competencia_id')) {
            $query->whereHas('competencias', function ($q) use ($request) {
                $q->where('competencias.id', $request->competencia_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('duracion_min')) {
            $query->where('duracion', '>=', $request->duracion_min);
        }

        if ($request->filled('duracion_max')) {
            $query->where('duracion', '<=', $request->duracion_max);
        }

        if ($ajaxSearch) {
            $orderBy = $request->get('order_by', 'codigo');
            $orderDirection = $request->get('order_direction', 'asc');
            $query->orderBy($orderBy, $orderDirection);
        } else {
            $query->orderBy('codigo', 'asc');
        }
    }
}
