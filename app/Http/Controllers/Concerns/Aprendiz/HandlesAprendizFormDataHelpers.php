<?php

namespace App\Http\Controllers\Concerns\Aprendiz;

use App\Models\FichaCaracterizacion;
use App\Models\Persona;
use Illuminate\Database\Eloquent\Collection;

trait HandlesAprendizFormDataHelpers
{
    /**
     * @return Collection<int, Persona>
     */
    protected function getAprendizPersonasDisponibles(): Collection
    {
        return Persona::whereDoesntHave('aprendiz')
            ->where('status', 1)
            ->get();
    }

    /**
     * @return Collection<int, FichaCaracterizacion>
     */
    protected function getAprendizFichasActivas(): Collection
    {
        return FichaCaracterizacion::where('status', 1)->get();
    }
}
