<?php

namespace App\Livewire\Instructores\Concerns\InstructorForm;

use App\Models\CentroFormacion;
use App\Models\ParametroTema;
use App\Models\Persona;
use App\Models\RedConocimiento;
use App\Models\Regional;
use Illuminate\Support\Facades\DB;

trait HandlesInstructorFormSelectDataHelpers
{
    private function cargarDatosSelects(): void
    {
        $this->personasDisponibles = Persona::query()
            ->whereDoesntHave('instructor')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('model_has_roles')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->whereRaw('model_has_roles.model_id = personas.id')
                    ->where('roles.name', 'INSTRUCTOR');
            })
            ->when($this->isEdit && $this->instructor, function ($query) {
                $query->orWhere('id', $this->instructor->persona_id);
            })
            ->orderBy('primer_nombre')
            ->orderBy('primer_apellido')
            ->get();

        $this->regionales = Regional::where('status', true)
            ->orderBy('nombre')
            ->get();

        $this->centrosFormacion = CentroFormacion::where('status', true)
            ->orderBy('nombre')
            ->get();

        $this->jornadasTrabajo = ParametroTema::where('tema_id', 23)
            ->where('status', true)
            ->with('parametro')
            ->get()
            ->sortBy(function ($pt) {
                return $pt->parametro->name ?? '';
            })
            ->values();

        $this->tiposVinculacion = ParametroTema::where('tema_id', 24)
            ->where('status', true)
            ->with('parametro')
            ->get()
            ->sortBy(function ($pt) {
                return $pt->parametro->name;
            })
            ->values();

        $this->nivelesAcademicos = ParametroTema::where('tema_id', 25)
            ->where('status', true)
            ->with('parametro')
            ->get()
            ->sortBy(function ($pt) {
                return $pt->parametro->name;
            })
            ->values();

        $this->redesConocimiento = RedConocimiento::where('status', true)
            ->orderBy('nombre')
            ->get();

        $this->modalidadesDisponibles = ParametroTema::where('tema_id', 5)
            ->where('status', true)
            ->with('parametro')
            ->get()
            ->sortBy(function ($pt) {
                return $pt->parametro->name;
            })
            ->values();
    }
}
