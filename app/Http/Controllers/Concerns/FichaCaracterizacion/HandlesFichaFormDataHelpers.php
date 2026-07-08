<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\ProgramaFormacion;

trait HandlesFichaFormDataHelpers
{
    protected function loadFichaFormCatalogs(): array
    {
        $programas = ProgramaFormacion::orderBy('nombre', 'asc')->get();
        $instructores = \App\Models\Instructor::with('persona')->orderBy('id', 'desc')->get();
        $ambientes = \App\Models\Ambiente::with('piso.bloque')->orderBy('title', 'asc')->get();
        $sedes = \App\Models\Sede::orderBy('sede', 'asc')->get();
        $modalidades = \App\Models\Parametro::whereHas('parametrosTemas', function ($query) {
            $query->where('tema_id', 5);
        })->orderBy('name', 'asc')->get();
        $jornadas = \App\Models\ParametroTema::whereHas('tema', function ($q) {
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

        return compact('programas', 'instructores', 'ambientes', 'sedes', 'modalidades', 'jornadas');
    }

    protected function loadFichaIndexFilterCatalogs(): array
    {
        $programas = $this->configuracionRepo->obtenerProgramasActivos();
        $instructores = \App\Models\Instructor::with('persona')->orderBy('id', 'desc')->get();
        $ambientes = \App\Models\Ambiente::with('piso.bloque')->orderBy('title', 'asc')->get();
        $sedes = \App\Models\Sede::orderBy('sede', 'asc')->get();
        $modalidades = \App\Models\Parametro::whereHas('parametrosTemas', function ($query) {
            $query->where('tema_id', 5);
        })->orderBy('name', 'asc')->get();
        $jornadas = \App\Models\ParametroTema::whereHas('tema', function ($q) {
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

        return compact('programas', 'instructores', 'ambientes', 'sedes', 'modalidades', 'jornadas');
    }
}
