<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\RedConocimiento;
use App\Models\Regional;
use App\Models\Tema;

trait HandlesInstructorFormDataEditHelpers
{
    protected function loadEditFormCatalogs(Instructor $instructor): array
    {
        $documentos = Tema::with([
            'parametros' => function ($query) {
                $query->wherePivot('status', 1);
            },
        ])->findOrFail(2);

        $generos = Tema::with([
            'parametros' => function ($query) {
                $query->wherePivot('status', 1);
            },
        ])->findOrFail(3);

        $regionales = Regional::where('status', 1)->get();

        $centrosFormacion = collect([]);
        if ($instructor->regional_id) {
            $centrosFormacion = \App\Models\CentroFormacion::where('regional_id', $instructor->regional_id)
                ->where('status', true)
                ->orderBy('nombre')
                ->get();
        }

        $especialidades = collect([]);
        if ($instructor->regional_id) {
            $especialidades = RedConocimiento::where('regionals_id', $instructor->regional_id)
                ->where('status', true)
                ->orderBy('nombre')
                ->get();
        }

        $tiposVinculacion = \App\Models\ParametroTema::whereHas('tema', function ($q) {
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

        $nivelesAcademicos = \App\Models\ParametroTema::whereHas('tema', function ($q) {
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

        return compact(
            'documentos',
            'generos',
            'regionales',
            'especialidades',
            'centrosFormacion',
            'tiposVinculacion',
            'nivelesAcademicos'
        );
    }
}
