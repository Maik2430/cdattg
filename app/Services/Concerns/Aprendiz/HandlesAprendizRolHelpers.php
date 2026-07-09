<?php

namespace App\Services\Concerns\Aprendiz;

use App\Models\Aprendiz;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

trait HandlesAprendizRolHelpers
{
    protected function asignarRolAprendizAlCrear(Aprendiz $aprendiz, array $datos): void
    {
        $aprendiz->load('persona.user');

        if (! $aprendiz->persona || ! $aprendiz->persona->user) {
            return;
        }

        $rolAprendiz = Role::firstOrCreate(['name' => 'APRENDIZ']);

        if (! $aprendiz->persona->user->hasRole('APRENDIZ')) {
            $aprendiz->persona->user->assignRole('APRENDIZ');

            Log::info('Rol APRENDIZ asignado al usuario al crear aprendiz', [
                'user_id' => $aprendiz->persona->user->id,
                'persona_id' => $aprendiz->persona_id,
                'aprendiz_id' => $aprendiz->id,
                'ficha_id' => $datos['ficha_caracterizacion_id'] ?? null,
            ]);
        }
    }

    protected function manejarRolAprendizEnActualizacion(Aprendiz $aprendiz, $fichaAnterior, $fichaNueva): void
    {
        $aprendiz->refresh();
        $aprendiz->load('persona.user');

        if (! $aprendiz->persona || ! $aprendiz->persona->user) {
            return;
        }

        $rolAprendiz = Role::firstOrCreate(['name' => 'APRENDIZ']);

        if (empty($fichaAnterior) && ! empty($fichaNueva)) {
            if (! $aprendiz->persona->user->hasRole('APRENDIZ')) {
                $aprendiz->persona->user->assignRole('APRENDIZ');

                Log::info('Rol APRENDIZ asignado al usuario al asignar ficha al aprendiz', [
                    'user_id' => $aprendiz->persona->user->id,
                    'persona_id' => $aprendiz->persona_id,
                    'aprendiz_id' => $aprendiz->id,
                    'ficha_id' => $fichaNueva,
                ]);
            }
        } elseif (! empty($fichaAnterior) && empty($fichaNueva)) {
            $this->removerRolAprendizSiSinOtraFicha($aprendiz, 'Rol APRENDIZ removido del usuario al desasignar ficha del aprendiz', [
                'ficha_anterior' => $fichaAnterior,
            ]);
        }
    }

    protected function removerRolAprendizAlEliminar(Aprendiz $aprendiz, int $id): void
    {
        if (! $aprendiz->persona || ! $aprendiz->persona->user) {
            return;
        }

        $this->removerRolAprendizSiSinOtraFicha($aprendiz, 'Rol APRENDIZ removido del usuario al eliminar aprendiz', [
            'aprendiz_id' => $id,
        ]);
    }

    protected function removerRolAprendizSiSinOtraFicha(Aprendiz $aprendiz, string $mensajeLog, array $contextoExtra = []): void
    {
        $tieneOtraFicha = Aprendiz::where('persona_id', $aprendiz->persona_id)
            ->where('id', '!=', $aprendiz->id)
            ->whereNotNull('ficha_caracterizacion_id')
            ->whereNull('deleted_at')
            ->exists();

        if (! $tieneOtraFicha && $aprendiz->persona->user->hasRole('APRENDIZ')) {
            $aprendiz->persona->user->removeRole('APRENDIZ');

            Log::info($mensajeLog, array_merge([
                'user_id' => $aprendiz->persona->user->id,
                'persona_id' => $aprendiz->persona_id,
                'aprendiz_id' => $aprendiz->id,
            ], $contextoExtra));
        }
    }
}
