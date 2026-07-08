<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaDestroyAuthValidationHelpers
{
    private function fichaDestroyResolveAuthorization($user, FichaCaracterizacion $ficha, string $id): bool
    {
        $puedeEliminar = false;

        if ($user->hasRole('SUPER ADMINISTRADOR')) {
            $puedeEliminar = true;
            Log::info('✓ Usuario SUPER ADMINISTRADOR detectado - acceso total concedido');
        } elseif ($user->hasRole('ADMINISTRADOR') && $user->can('ELIMINAR FICHA CARACTERIZACION')) {
            $puedeEliminar = true;
            Log::info('✓ Usuario ADMINISTRADOR con permiso ELIMINAR FICHA CARACTERIZACION');
        } else {
            try {
                $this->authorize('delete', $ficha);
                $puedeEliminar = true;
                Log::info('✓ Autorización verificada por política: Usuario autorizado para eliminar la ficha');
            } catch (\Illuminate\Auth\Access\AuthorizationException $authException) {
                Log::error('✗ Autorización denegada por política', [
                    'user_id' => $user->id,
                    'ficha_id' => $id,
                    'error' => 'El usuario no tiene permiso para eliminar esta ficha',
                    'user_roles' => $user->roles->pluck('name')->toArray(),
                    'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                    'required_permission' => 'ELIMINAR FICHA CARACTERIZACION',
                ]);
                $puedeEliminar = false;
            }
        }

        return $puedeEliminar;
    }

    private function fichaDestroyValidateDeletion(FichaCaracterizacion $ficha, string $id, $user): ?\Illuminate\Http\RedirectResponse
    {
        $aprendicesCount = $ficha->contarAprendices();
        Log::info('Verificando aprendices asignados', [
            'tiene_aprendices' => $ficha->tieneAprendices(),
            'cantidad_aprendices' => $aprendicesCount,
        ]);

        if ($ficha->tieneAprendices()) {
            Log::warning('✗ VALIDACIÓN FALLIDA: Ficha con aprendices asignados', [
                'ficha_id' => $id,
                'numero_ficha' => $ficha->ficha,
                'aprendices_count' => $aprendicesCount,
                'user_id' => $user->id,
                'razon' => 'No se puede eliminar una ficha que tiene aprendices asignados',
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'No se puede eliminar la ficha porque tiene '.$aprendicesCount.' aprendice(s) asignado(s).');
        }

        Log::info('✓ Validación de aprendices pasada: La ficha no tiene aprendices asignados');

        $instructoresCount = $ficha->instructorFicha()->count();
        Log::info('Verificando instructores asignados', [
            'cantidad_instructores' => $instructoresCount,
        ]);

        $tieneAsistencias = DB::table('asistencia_aprendices')
            ->join('aprendices', 'asistencia_aprendices.aprendiz_id', '=', 'aprendices.id')
            ->where('aprendices.ficha_caracterizacion_id', $id)
            ->whereNull('aprendices.deleted_at')
            ->exists();

        Log::info('Verificando asistencias registradas', [
            'tiene_asistencias' => $tieneAsistencias,
        ]);

        if ($tieneAsistencias) {
            Log::warning('✗ VALIDACIÓN FALLIDA: Ficha con asistencias registradas', [
                'ficha_id' => $id,
                'numero_ficha' => $ficha->ficha,
                'razon' => 'No se puede eliminar una ficha que tiene asistencias registradas',
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'No se puede eliminar la ficha porque tiene asistencias registradas.');
        }

        return null;
    }
}
