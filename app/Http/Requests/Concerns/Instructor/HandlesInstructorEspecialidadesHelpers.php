<?php

namespace App\Http\Requests\Concerns\Instructor;

use App\Models\RedConocimiento;
use App\Models\Regional;

trait HandlesInstructorEspecialidadesHelpers
{
    protected function validateEspecialidadesPorRegional($especialidades, $regionalId, $validator): bool
    {
        if (empty($especialidades)) {
            return true;
        }

        $regional = Regional::find($regionalId);
        if (! $regional) {
            return false;
        }

        $redesConocimiento = RedConocimiento::where('regionals_id', $regionalId)
            ->where('status', true)
            ->pluck('nombre')
            ->toArray();

        if (! empty($especialidades['principal'])) {
            if (! in_array($especialidades['principal'], $redesConocimiento)) {
                return false;
            }
        }

        if (! empty($especialidades['secundarias']) && is_array($especialidades['secundarias'])) {
            foreach ($especialidades['secundarias'] as $especialidad) {
                if (! in_array($especialidad, $redesConocimiento)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function hasConflictsWithNewFichas($instructorId, $validator): bool
    {
        if (! $instructorId) {
            return false;
        }

        return false;
    }

    protected function isCreating(): bool
    {
        return $this->route('instructor') === null;
    }

    protected function isUpdating(): bool
    {
        return $this->route('instructor') !== null;
    }
}
