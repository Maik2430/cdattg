<?php

namespace App\Services\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\RedConocimiento;

trait HandlesInstructorEspecialidadesHelpers
{
    /**
     * Asigna especialidades a un instructor
     *
     * @param  array<int>  $especialidadesIds
     */
    protected function asignarEspecialidades(Instructor $instructor, array $especialidadesIds): void
    {
        if (empty($especialidadesIds)) {
            return;
        }

        $especialidadesValidas = RedConocimiento::whereIn('id', $especialidadesIds)
            ->pluck('id')
            ->toArray();

        $especialidadesFormateadas = [
            'principal' => null,
            'secundarias' => [],
        ];

        if (! empty($especialidadesValidas)) {
            $especialidadesFormateadas['principal'] = $especialidadesValidas[0];

            for ($i = 1; $i < count($especialidadesValidas); $i++) {
                $especialidadesFormateadas['secundarias'][] = $especialidadesValidas[$i];
            }
        }

        $instructor->especialidades = $especialidadesFormateadas;
        $instructor->save();
    }
}
