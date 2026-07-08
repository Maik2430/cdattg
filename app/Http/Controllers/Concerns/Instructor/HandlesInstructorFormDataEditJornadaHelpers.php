<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorFormDataEditJornadaHelpers
{
    protected function loadEditJornadaFormCatalogs(Instructor $instructor): array
    {
        $temaJornadas = \App\Models\Tema::where('name', 'JORNADAS')->first();

        if ($temaJornadas) {
            $jornadasTrabajo = \App\Models\ParametroTema::where('tema_id', $temaJornadas->id)
                ->whereHas('parametro', function ($query) {
                    $query->where('status', true);
                })
                ->where('status', true)
                ->with(['parametro', 'tema'])
                ->get()
                ->sortBy(function ($pt) {
                    return $pt->parametro->name ?? '';
                })
                ->values();
        } else {
            $jornadasTrabajo = \App\Models\ParametroTema::whereHas('tema', function ($q) {
                $q->where('name', 'LIKE', '%JORNADAS%');
            })->whereHas('parametro', function ($query) {
                $query->where('status', true);
            })->where('status', true)
                ->with(['parametro', 'tema'])
                ->get()
                ->sortBy(function ($pt) {
                    return $pt->parametro->name ?? '';
                })
                ->values();
        }

        Log::info('Jornadas de trabajo cargadas en edit', [
            'cantidad' => $jornadasTrabajo->count(),
            'tema_id' => $temaJornadas->id ?? null,
            'jornadas' => $jornadasTrabajo->map(function ($j) {
                return [
                    'id' => $j->id,
                    'parametro_id' => $j->parametro_id,
                    'nombre' => $j->parametro->name ?? 'Sin nombre',
                    'tema' => $j->tema->name ?? 'Sin tema',
                    'status' => $j->status,
                ];
            })->toArray(),
        ]);

        $jornadasAsignadas = [];
        try {
            if ($instructor->jornadas && is_array($instructor->jornadas) && ! empty($instructor->jornadas)) {
                $jornadasIds = $instructor->jornadas;
                $jornadasAsignadas = array_map('intval', $jornadasIds);
            } elseif ($instructor->jornadas()->exists()) {
                $parametrosTemas = $instructor->jornadas()->get();
                if ($parametrosTemas->isNotEmpty()) {
                    $jornadasAsignadas = $parametrosTemas->pluck('id')->toArray();
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error al obtener jornadas asignadas del instructor: '.$e->getMessage());
            $jornadasAsignadas = [];
        }

        $modalidadesFormacion = \App\Models\ParametroTema::whereHas('tema', function ($q) {
            $q->where('id', 5);
        })->whereHas('parametro', function ($query) {
            $query->where('status', true);
        })->where('status', true)
            ->with('parametro')
            ->get()
            ->sortBy(function ($pt) {
                return $pt->parametro->name ?? '';
            })
            ->values();

        return compact('jornadasTrabajo', 'jornadasAsignadas', 'modalidadesFormacion');
    }
}
