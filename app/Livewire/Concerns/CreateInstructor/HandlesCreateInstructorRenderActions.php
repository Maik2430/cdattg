<?php

namespace App\Livewire\Concerns\CreateInstructor;

use App\Models\Persona;
use App\Models\RedConocimiento;
use App\Models\Regional;

trait HandlesCreateInstructorRenderActions
{
    use HandlesCreateInstructorRenderQueryHelpers;

    public function render()
    {
        $personasDisponibles = Persona::query()
            ->whereDoesntHave('instructor')
            ->orderBy('primer_nombre')
            ->orderBy('primer_apellido')
            ->get();

        $regionales = Regional::where('status', 1)->get();
        $especialidades = RedConocimiento::where('status', true)->orderBy('nombre')->get();

        return view('livewire.create-instructor', [
            'personasDisponibles' => $personasDisponibles,
            'regionales' => $regionales,
            'especialidadesList' => $especialidades,
            'jornadasTrabajo' => $this->loadJornadasTrabajo(),
            'tiposVinculacion' => $this->loadTiposVinculacion(),
            'nivelesAcademicos' => $this->loadNivelesAcademicos(),
            'modalidadesFormacion' => $this->loadModalidadesFormacion(),
        ]);
    }
}
