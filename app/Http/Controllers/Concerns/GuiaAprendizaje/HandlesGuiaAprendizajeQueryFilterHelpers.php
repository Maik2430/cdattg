<?php

namespace App\Http\Controllers\Concerns\GuiaAprendizaje;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HandlesGuiaAprendizajeQueryFilterHelpers
{
    /**
     * @param  Builder<\App\Models\GuiasAprendizaje>  $query
     */
    protected function applyGuiaAprendizajeListFilters(Builder $query, Request $request, bool $ajaxSearch = false): void
    {
        $searchField = $ajaxSearch ? 'q' : 'search';

        if ($request->filled($searchField)) {
            $searchTerm = $request->input($searchField);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('codigo', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('nombre', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('descripcion', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('codigo')) {
            $query->where('codigo', 'LIKE', "%{$request->codigo}%");
        }

        if ($request->filled('nombre')) {
            $query->where('nombre', 'LIKE', "%{$request->nombre}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (! $ajaxSearch && $request->filled('nivel_dificultad')) {
            $query->where('nivel_dificultad', $request->nivel_dificultad);
        }

        if ($request->filled('programa_id')) {
            $query->whereHas('resultadosAprendizaje.competencias.programas', function ($q) use ($request) {
                $q->where('programa_formacion.id', $request->programa_id);
            });
        }

        if ($request->filled('competencia_id')) {
            $query->whereHas('resultadosAprendizaje.competencias', function ($q) use ($request) {
                $q->where('competencias.id', $request->competencia_id);
            });
        }

        if ($request->filled('resultado_id')) {
            $query->whereHas('resultadosAprendizaje', function ($q) use ($request) {
                $q->where('resultados_aprendizajes.id', $request->resultado_id);
            });
        }

        if (! $ajaxSearch && $request->filled('user_create_id')) {
            $query->where('user_create_id', $request->user_create_id);
        }

        if (! $ajaxSearch && $request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if (! $ajaxSearch && $request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
    }
}
