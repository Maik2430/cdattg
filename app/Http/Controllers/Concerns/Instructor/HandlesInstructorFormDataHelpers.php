<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Persona;

trait HandlesInstructorFormDataHelpers
{
    protected function loadIndexFormCatalogs(): array
    {
        $personasDisponibles = Persona::query()
            ->whereDoesntHave('instructor')
            ->orderBy('primer_nombre')
            ->orderBy('primer_apellido')
            ->get();

        $jornadasTrabajo = \App\Models\ParametroTema::whereHas('tema', function ($q) {
            $q->where('name', 'LIKE', '%JORNADAS%');
        })->whereHas('parametro', function ($query) {
            $query->where('status', true);
        })->where('status', true)
            ->with('parametro')
            ->get()
            ->sortBy(function ($pt) {
                return $pt->parametro->name ?? '';
            })
            ->values();

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

        return compact('personasDisponibles', 'jornadasTrabajo', 'tiposVinculacion', 'nivelesAcademicos');
    }
}
