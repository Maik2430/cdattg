<?php

namespace App\Services\Concerns\AprendizRole;

use App\Models\Aprendiz;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

trait HandlesAprendizRoleEnsureActions
{
    public function ensureAprendizRole(Aprendiz $aprendiz): bool
    {
        try {
            $persona = $aprendiz->persona;
            if (! $persona) {
                Log::warning('Aprendiz sin persona asociada en AprendizRoleService', [
                    'aprendiz_id' => $aprendiz->id,
                    'persona_id' => $aprendiz->persona_id,
                ]);

                return false;
            }

            if (! $persona->user) {
                $user = $this->createUserForPersona($persona, $aprendiz);
                if (! $user) {
                    return false;
                }
            } else {
                $user = $persona->user;
            }

            Role::firstOrCreate(['name' => 'APRENDIZ']);

            if (! $user->hasRole('APRENDIZ')) {
                $user->assignRole('APRENDIZ');

                Log::info('Rol APRENDIZ asignado por AprendizRoleService', [
                    'aprendiz_id' => $aprendiz->id,
                    'user_id' => $user->id,
                    'persona_id' => $persona->id,
                    'ficha_id' => $aprendiz->ficha_caracterizacion_id,
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error en AprendizRoleService::ensureAprendizRole', [
                'aprendiz_id' => $aprendiz->id,
                'persona_id' => $aprendiz->persona_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }
}
