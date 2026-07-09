<?php

namespace App\Http\Controllers\Concerns\Caracterizacion;

use App\Models\ParametroTema;

trait HandlesCaracterizacionFormDataHelpers
{
    protected function getCaracterizacionJornadas()
    {
        return ParametroTema::whereHas('tema', function ($q) {
            $q->where('name', 'LIKE', '%JORNADAS%');
        })->whereHas('parametro', function ($query) {
            $query->where('status', true);
        })->where('status', true)
            ->with('parametro')
            ->get();
    }
}
