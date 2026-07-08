<?php

namespace App\Livewire\Fichas\Concerns\FichaForm;

use App\Models\Ambiente;
use App\Models\Instructor;
use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\ProgramaFormacion;
use App\Models\Sede;

trait HandlesFichaFormSelectDataHelpers
{
    private function cargarDatosSelects(): void
    {
        $this->programas = ProgramaFormacion::with('redConocimiento.regional')
            ->orderBy('nombre')
            ->get();

        $this->sedes = Sede::with('regional')
            ->orderBy('sede')
            ->get();

        $this->instructores = Instructor::with('persona')
            ->whereHas('persona', function ($query) {
                $query->where('status', 1);
            })
            ->orderBy('id', 'desc')
            ->get();

        $this->ambientes = Ambiente::where('status', 1)
            ->orderBy('title')
            ->get();

        $this->modalidades = Parametro::whereHas('parametrosTemas', function ($query) {
            $query->where('tema_id', 5);
        })->orderBy('name', 'asc')->get();

        $this->jornadas = ParametroTema::whereHas('tema', function ($q) {
            $q->where('name', 'LIKE', '%JORNADAS%');
        })->whereHas('parametro', function ($query) {
            $query->where('status', true);
        })->where('status', true)
            ->with('parametro')
            ->get()
            ->sortBy(function ($pt) {
                return $pt->parametro->name;
            });
    }
}
