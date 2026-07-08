<?php

namespace App\Livewire\Concerns\CreateInstructor;

use App\Models\ParametroTema;
use Illuminate\Support\Facades\Log;

trait HandlesCreateInstructorRenderQueryHelpers
{
    protected function loadJornadasTrabajo()
    {
        return ParametroTema::whereHas('tema', function ($q) {
            $q->where('name', 'LIKE', '%JORNADAS%');
        })->whereHas('parametro', function ($query) {
            $query->where('status', true);
        })->where('status', true)
            ->with('parametro')
            ->get()
            ->sortBy(function ($pt) {
                return $pt->parametro->name;
            })
            ->values();
    }

    protected function loadTiposVinculacion()
    {
        $tiposVinculacion = ParametroTema::whereHas('tema', function ($q) {
            $q->where('name', 'LIKE', '%TIPOS DE VINCULACION%');
        })->whereHas('parametro', function ($query) {
            $query->where('status', true);
        })->where('status', true)
            ->with('parametro')
            ->get()
            ->sortBy(function ($pt) {
                return $pt->parametro->name;
            })
            ->values();

        Log::info('Tipos de vinculación cargados en CreateInstructor', [
            'cantidad' => $tiposVinculacion->count(),
            'tipos' => $tiposVinculacion->pluck('parametro.name', 'id')->toArray(),
        ]);

        return $tiposVinculacion;
    }

    protected function loadNivelesAcademicos()
    {
        $nivelesAcademicos = ParametroTema::whereHas('tema', function ($q) {
            $q->where('name', 'LIKE', '%NIVELES ACADEMICOS%');
        })->whereHas('parametro', function ($query) {
            $query->where('status', true);
        })->where('status', true)
            ->with('parametro')
            ->get()
            ->sortBy(function ($pt) {
                return $pt->parametro->name;
            })
            ->values();

        Log::info('Niveles académicos cargados en CreateInstructor', [
            'cantidad' => $nivelesAcademicos->count(),
            'niveles' => $nivelesAcademicos->pluck('parametro.name', 'id')->toArray(),
        ]);

        return $nivelesAcademicos;
    }

    protected function loadModalidadesFormacion()
    {
        return ParametroTema::whereHas('tema', function ($q) {
            $q->where('id', 5);
        })->whereHas('parametro', function ($query) {
            $query->where('status', true);
        })->where('status', true)
            ->with('parametro')
            ->get()
            ->sortBy(function ($pt) {
                return $pt->parametro->name;
            })
            ->values();
    }
}
