<?php

namespace App\Http\Controllers\Concerns\ProgramaFormacion;

use App\Models\Parametro;
use App\Models\RedConocimiento;
use Illuminate\Database\Eloquent\Collection;

trait HandlesProgramaFormacionFormDataHelpers
{
    /**
     * @return Collection<int, RedConocimiento>
     */
    protected function getProgramaFormacionRedesConocimiento(): Collection
    {
        return RedConocimiento::all();
    }

    /**
     * @return Collection<int, Parametro>
     */
    protected function getProgramaFormacionNivelesFormacion(): Collection
    {
        return Parametro::whereHas('temas', function ($query) {
            $query->where('temas.id', 6);
        })->get();
    }
}
