<?php

namespace App\Services\Concerns\AprendizRole;

use App\Models\Aprendiz;
use Illuminate\Support\Facades\Log;

trait HandlesAprendizRoleSyncActions
{
    public function syncRolesWithUser(Aprendiz $aprendiz): void
    {
        try {
            if ($aprendiz->persona && $aprendiz->persona->user) {
                $userRoles = $aprendiz->persona->user->roles->pluck('name')->toArray();
                $aprendiz->syncRoles($userRoles);

                Log::info('Roles sincronizados entre Aprendiz y User', [
                    'aprendiz_id' => $aprendiz->id,
                    'user_id' => $aprendiz->persona->user->id,
                    'roles' => $userRoles,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al sincronizar roles en AprendizRoleService', [
                'aprendiz_id' => $aprendiz->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function removeAprendizRole(Aprendiz $aprendiz): bool
    {
        try {
            $persona = $aprendiz->persona;
            if ($persona && $persona->user) {
                $persona->user->removeRole('APRENDIZ');

                Log::info('Rol APRENDIZ removido por AprendizRoleService', [
                    'aprendiz_id' => $aprendiz->id,
                    'user_id' => $persona->user->id,
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error al remover rol APRENDIZ en AprendizRoleService', [
                'aprendiz_id' => $aprendiz->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
