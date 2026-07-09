<?php

namespace App\Repositories\Concerns\Configuracion;

use App\Models\Departamento;
use App\Models\FichaCaracterizacion;
use App\Models\Municipio;
use App\Models\Parametro;
use App\Models\ProgramaFormacion;
use App\Models\RedConocimiento;
use App\Models\Regional;
use App\Models\Tema;
use Illuminate\Database\Eloquent\Collection;

trait HandlesConfiguracionRepositoryQueryActions
{
    public function obtenerFichasActivas(): Collection
    {
        return $this->cache('fichas.activas', function () {
            return FichaCaracterizacion::where('status', 1)
                ->with(['programaFormacion', 'jornadaFormacion.parametro'])
                ->orderBy('ficha')
                ->get();
        }, 60);
    }

    public function obtenerRegionalesActivas(): Collection
    {
        return $this->cache('regionales.activas', function () {
            return Regional::where('status', true)
                ->orderBy('nombre')
                ->get();
        }, 720);
    }

    public function obtenerTemasConParametros(): Collection
    {
        return $this->cache('temas.parametros', function () {
            return Tema::with(['parametros' => function ($query): void {
                $query->wherePivot('status', 1);
            }])->get();
        }, 1440);
    }

    public function obtenerTemaConParametros(int $temaId): ?Tema
    {
        return $this->cache("tema.{$temaId}.parametros", function () use ($temaId) {
            return Tema::with(['parametros' => function ($query): void {
                $query->wherePivot('status', 1);
            }])->find($temaId);
        }, 1440);
    }

    public function obtenerRedesConocimiento(): Collection
    {
        return $this->cache('redes.conocimiento', function () {
            return RedConocimiento::where('status', true)
                ->orderBy('nombre')
                ->get();
        }, 720);
    }

    public function obtenerProgramasActivos(): Collection
    {
        return $this->cache('programas.activos', function () {
            return ProgramaFormacion::where('status', true)
                ->with(['redConocimiento', 'nivelFormacion'])
                ->orderBy('nombre')
                ->get();
        }, 360);
    }

    public function obtenerDepartamentos(): Collection
    {
        return $this->cache('departamentos.todos', function () {
            return Departamento::orderBy('nombre')->get();
        }, 1440);
    }

    public function obtenerMunicipiosPorDepartamento(int $departamentoId): Collection
    {
        return $this->cache("municipios.departamento.{$departamentoId}", function () use ($departamentoId) {
            return Municipio::where('departamento_id', $departamentoId)
                ->orderBy('nombre')
                ->get();
        }, 1440);
    }

    public function obtenerParametrosSistema(): Collection
    {
        return $this->cache('parametros.sistema', function () {
            return Parametro::where('status', true)->get();
        }, 1440);
    }
}
