<?php

namespace App\Livewire\Instructores\Concerns;

use App\Models\RedConocimiento;

trait HandlesInstructorIndexEspecialidadesHelpers
{
    public function obtenerEspecialidadesFormateadas($instructor)
    {
        $especialidades = $instructor->especialidades ?? [];
        $especialidadPrincipalId = $especialidades['principal'] ?? null;
        $especialidadesSecundariasIds = $especialidades['secundarias'] ?? [];

        $especialidadPrincipalNombre = null;
        if ($especialidadPrincipalId) {
            $redConocimiento = RedConocimiento::find($especialidadPrincipalId);
            $especialidadPrincipalNombre = $redConocimiento ? $redConocimiento->nombre : null;
        }

        $especialidadesSecundariasNombres = [];
        if (! empty($especialidadesSecundariasIds)) {
            $redesConocimiento = RedConocimiento::whereIn('id', $especialidadesSecundariasIds)->get();
            $especialidadesSecundariasNombres = $redesConocimiento->pluck('nombre')->toArray();
        }

        return [
            'principal' => $especialidadPrincipalNombre,
            'secundarias' => $especialidadesSecundariasNombres,
        ];
    }
}
