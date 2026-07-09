<?php

namespace App\Http\Controllers\Concerns\ProgramaFormacion;

use App\Models\ProgramaFormacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HandlesProgramaFormacionQueryFilterHelpers
{
    /**
     * @param  Builder<ProgramaFormacion>  $query
     */
    protected function applyProgramaFormacionSearchFilters(Builder $query, Request $request): void
    {
        $search = $request->input('search');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                    ->orWhere('nombre', 'LIKE', "%{$search}%")
                    ->orWhereHas('redConocimiento', function ($subQuery) use ($search) {
                        $subQuery->where('nombre', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('nivelFormacion', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $redConocimientoId = $request->input('red_conocimiento_id');
        if (! empty($redConocimientoId)) {
            $query->where('red_conocimiento_id', $redConocimientoId);
        }

        $nivelFormacionId = $request->input('nivel_formacion_id');
        if (! empty($nivelFormacionId)) {
            $query->where('nivel_formacion_id', $nivelFormacionId);
        }

        $status = $request->input('status');
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $query->orderBy('nombre', 'asc');
    }
}
