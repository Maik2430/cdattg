<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\RedConocimiento;

trait HandlesInstructorEspecialidadActions
{
    /**
     * Mostrar especialidades del instructor
     */
    public function especialidades(Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);

        $especialidades = $instructor->especialidades;

        return view('instructores.especialidades', compact('instructor', 'especialidades'));
    }

    /**
     * Gestionar especialidades del instructor
     */
    public function gestionarEspecialidades(Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);

        // Obtener redes de conocimiento disponibles según la regional del instructor
        $redesConocimiento = RedConocimiento::where('regionals_id', $instructor->regional_id)
            ->where('status', true)
            ->orderBy('nombre')
            ->get();

        // Obtener especialidades actuales del instructor
        $especialidadesActuales = $instructor->especialidades ?? [];

        // Separar especialidades principales y secundarias (ahora son IDs)
        $especialidadPrincipalId = $especialidadesActuales['principal'] ?? null;
        $especialidadesSecundariasIds = $especialidadesActuales['secundarias'] ?? [];

        // Obtener los nombres de las especialidades basándose en los IDs
        $especialidadPrincipal = null;
        if ($especialidadPrincipalId) {
            $redConocimiento = RedConocimiento::find($especialidadPrincipalId);
            $especialidadPrincipal = $redConocimiento ? $redConocimiento->nombre : null;
        }

        $especialidadesSecundarias = [];
        if (! empty($especialidadesSecundariasIds)) {
            $redesConocimiento = RedConocimiento::whereIn('id', $especialidadesSecundariasIds)->get();
            $especialidadesSecundarias = $redesConocimiento->pluck('nombre')->toArray();
        }

        return view(
            'instructores.gestionar-especialidades',
            compact(
                'instructor',
                'redesConocimiento',
                'especialidadPrincipal',
                'especialidadesSecundarias'
            )
        );
    }
}
