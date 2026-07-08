<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;

trait HandlesFichaSearchQueryHelpers
{
    private function buildFichaSearchQuery(Request $request)
    {
        $query = FichaCaracterizacion::with([
            'programaFormacion',
            'instructor.persona',
            'ambiente.piso.bloque',
            'modalidadFormacion',
            'sede',
            'jornadaFormacion.parametro',
            'aprendices',
        ]);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('ficha', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('programaFormacion', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('nombre', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('codigo', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('instructor.persona', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('primer_nombre', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('segundo_nombre', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('primer_apellido', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('segundo_apellido', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('numero_documento', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('ambiente', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('title', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        if ($request->filled('programa_id')) {
            $query->where('programa_formacion_id', $request->input('programa_id'));
        }
        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->input('instructor_id'));
        }
        if ($request->filled('ambiente_id')) {
            $query->where('ambiente_id', $request->input('ambiente_id'));
        }
        if ($request->filled('sede_id')) {
            $query->where('sede_id', $request->input('sede_id'));
        }
        if ($request->filled('modalidad_id')) {
            $query->where('modalidad_formacion_id', $request->input('modalidad_id'));
        }
        if ($request->filled('jornada_id')) {
            $query->where('jornada_id', $request->input('jornada_id'));
        }
        if ($request->filled('estado')) {
            $query->where('status', $request->input('estado'));
        }
        if ($request->filled('fecha_inicio_desde')) {
            $query->where('fecha_inicio', '>=', $request->input('fecha_inicio_desde'));
        }
        if ($request->filled('fecha_inicio_hasta')) {
            $query->where('fecha_inicio', '<=', $request->input('fecha_inicio_hasta'));
        }
        if ($request->filled('fecha_fin_desde')) {
            $query->where('fecha_fin', '>=', $request->input('fecha_fin_desde'));
        }
        if ($request->filled('fecha_fin_hasta')) {
            $query->where('fecha_fin', '<=', $request->input('fecha_fin_hasta'));
        }
        if ($request->filled('con_aprendices')) {
            if ($request->input('con_aprendices') == '1') {
                $query->has('aprendices');
            } else {
                $query->doesntHave('aprendices');
            }
        }

        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $allowedSortFields = ['id', 'ficha', 'fecha_inicio', 'fecha_fin', 'total_horas', 'created_at'];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query;
    }

    private function formatFichaSearchJson($ficha): array
    {
        return [
            'id' => $ficha->id,
            'ficha' => $ficha->ficha,
            'status' => $ficha->status,
            'fecha_inicio' => $ficha->fecha_inicio,
            'fecha_fin' => $ficha->fecha_fin,
            'total_horas' => $ficha->total_horas,
            'programa_formacion' => [
                'id' => $ficha->programaFormacion->id ?? null,
                'nombre' => $ficha->programaFormacion->nombre ?? 'N/A',
                'codigo' => $ficha->programaFormacion->codigo ?? 'N/A',
            ],
            'instructor_principal' => [
                'id' => $ficha->instructor->id ?? null,
                'persona' => [
                    'id' => $ficha->instructor->persona->id ?? null,
                    'primer_nombre' => $ficha->instructor->persona->primer_nombre ?? 'N/A',
                    'primer_apellido' => $ficha->instructor->persona->primer_apellido ?? 'N/A',
                ],
            ],
            'sede' => [
                'id' => $ficha->sede->id ?? null,
                'sede' => $ficha->sede->sede ?? 'N/A',
            ],
            'modalidad_formacion' => [
                'id' => $ficha->modalidadFormacion->id ?? null,
                'nombre' => $ficha->modalidadFormacion->name ?? 'N/A',
            ],
            'ambiente' => [
                'id' => $ficha->ambiente->id ?? null,
                'title' => $ficha->ambiente->title ?? 'N/A',
            ],
            'aprendices_count' => $ficha->aprendices->count() ?? 0,
            'created_at' => $ficha->created_at,
            'updated_at' => $ficha->updated_at,
        ];
    }
}
