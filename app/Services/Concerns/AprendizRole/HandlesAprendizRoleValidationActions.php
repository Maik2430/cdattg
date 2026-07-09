<?php

namespace App\Services\Concerns\AprendizRole;

use App\Models\Aprendiz;

trait HandlesAprendizRoleValidationActions
{
    public function validateRoleConsistency(Aprendiz $aprendiz): array
    {
        $issues = [];

        try {
            if (! $aprendiz->persona) {
                $issues[] = 'Aprendiz sin persona asociada';

                return $issues;
            }

            if (! $aprendiz->persona->user) {
                $issues[] = 'Persona sin usuario asociado';

                return $issues;
            }

            $user = $aprendiz->persona->user;
            $userRoles = $user->roles->pluck('name')->toArray();
            $aprendizRoles = $aprendiz->roles->pluck('name')->toArray();

            if (! in_array('APRENDIZ', $userRoles)) {
                $issues[] = 'Usuario no tiene rol APRENDIZ';
            }

            if (! in_array('APRENDIZ', $aprendizRoles)) {
                $issues[] = 'Aprendiz no tiene rol APRENDIZ';
            }

            $missingInAprendiz = array_diff($userRoles, $aprendizRoles);
            $missingInUser = array_diff($aprendizRoles, $userRoles);

            if (! empty($missingInAprendiz)) {
                $issues[] = 'Roles faltantes en Aprendiz: '.implode(', ', $missingInAprendiz);
            }

            if (! empty($missingInUser)) {
                $issues[] = 'Roles faltantes en User: '.implode(', ', $missingInUser);
            }
        } catch (\Exception $e) {
            $issues[] = 'Error al validar consistencia: '.$e->getMessage();
        }

        return $issues;
    }
}
