<?php

namespace App\Http\Controllers\Concerns\Competencia;

use App\Models\Competencia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HandlesCompetenciaQueryFilterHelpers
{
    /**
     * @param  Builder<Competencia>  $query
     */
    protected function applyCompetenciaSearchFilters(Builder $query, Request $request): void
    {
        if ($request->filled('q')) {
            $searchTerm = $request->q;
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

        if ($request->filled('programa_id')) {
            $query->whereHas('programasFormacion', function ($q) use ($request) {
                $q->where('programas_formacion.id', $request->programa_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('fecha_inicio_desde')) {
            $query->whereDate('fecha_inicio', '>=', $request->fecha_inicio_desde);
        }

        if ($request->filled('fecha_inicio_hasta')) {
            $query->whereDate('fecha_inicio', '<=', $request->fecha_inicio_hasta);
        }

        if ($request->filled('fecha_fin_desde')) {
            $query->whereDate('fecha_fin', '>=', $request->fecha_fin_desde);
        }

        if ($request->filled('fecha_fin_hasta')) {
            $query->whereDate('fecha_fin', '<=', $request->fecha_fin_hasta);
        }

        if ($request->filled('duracion_min')) {
            $query->where('duracion', '>=', $request->duracion_min);
        }

        if ($request->filled('duracion_max')) {
            $query->where('duracion', '<=', $request->duracion_max);
        }

        if ($request->filled('vigentes') && $request->vigentes == 1) {
            $query->vigentes();
        }

        $orderBy = $request->get('order_by', 'codigo');
        $orderDirection = $request->get('order_direction', 'asc');
        $query->orderBy($orderBy, $orderDirection);
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatCompetenciaForSearch(Competencia $competencia): array
    {
        return [
            'id' => $competencia->id,
            'codigo' => $competencia->codigo,
            'nombre' => $competencia->nombre,
            'descripcion' => $competencia->descripcion,
            'duracion' => $competencia->duracion,
            'fecha_inicio' => $competencia->fecha_inicio ? $competencia->fecha_inicio->format('d/m/Y') : null,
            'fecha_fin' => $competencia->fecha_fin ? $competencia->fecha_fin->format('d/m/Y') : null,
            'status' => $competencia->status,
            'estado_texto' => $competencia->status ? 'Activa' : 'Inactiva',
            'programas_count' => $competencia->programasFormacion->count(),
            'resultados_count' => $competencia->resultadosAprendizaje()->count(),
            'created_at' => $competencia->created_at->format('d/m/Y H:i'),
            'user_create' => $competencia->userCreate ? $competencia->userCreate->name : 'N/A',
        ];
    }
}
