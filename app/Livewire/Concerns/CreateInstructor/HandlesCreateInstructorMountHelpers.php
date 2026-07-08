<?php

namespace App\Livewire\Concerns\CreateInstructor;

use App\Models\CentroFormacion;

trait HandlesCreateInstructorMountHelpers
{
    public function mount(): void
    {
        if (old('titulos_obtenidos')) {
            $this->titulos_obtenidos = old('titulos_obtenidos');
        }
        if (old('instituciones_educativas')) {
            $this->instituciones_educativas = old('instituciones_educativas');
        }
        if (old('certificaciones_tecnicas')) {
            $this->certificaciones_tecnicas = old('certificaciones_tecnicas');
        }
        if (old('cursos_complementarios')) {
            $this->cursos_complementarios = old('cursos_complementarios');
        }
        if (old('areas_experticia')) {
            $this->areas_experticia = is_array(old('areas_experticia')) ? old('areas_experticia') : [old('areas_experticia')];
        }
        if (old('competencias_tic')) {
            $this->competencias_tic = is_array(old('competencias_tic')) ? old('competencias_tic') : [old('competencias_tic')];
        }
        if (old('idiomas')) {
            $this->idiomas = old('idiomas');
        }
        if (old('regional_id')) {
            $this->regional_id = old('regional_id');
            $this->updatedRegionalId();
        }
    }

    public function updatedRegionalId(): void
    {
        $this->centro_formacion_id = null;

        if ($this->regional_id) {
            $this->centrosFormacion = CentroFormacion::where('regional_id', $this->regional_id)
                ->where('status', true)
                ->orderBy('nombre')
                ->get(['id', 'nombre'])
                ->toArray();
        } else {
            $this->centrosFormacion = [];
        }
    }
}
