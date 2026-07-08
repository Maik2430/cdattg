<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesFichaAprendicesDesassignIndividualHelpers
{
    private function desasignarAprendizIndividual(int $aprendizId, string $fichaId): ?array
    {
        $aprendiz = \App\Models\Aprendiz::with('persona.user')->find($aprendizId);
        if (! $aprendiz) {
            Log::warning('Aprendiz no encontrado durante actualización', [
                'aprendiz_id' => $aprendizId,
                'user_id' => Auth::id(),
            ]);

            return null;
        }

        $estadoAnterior = $aprendiz->estado;
        $fichaAnterior = $aprendiz->ficha_caracterizacion_id;

        $tieneOtraFicha = \App\Models\Aprendiz::where('persona_id', $aprendiz->persona_id)
            ->where('id', '!=', $aprendiz->id)
            ->whereNotNull('ficha_caracterizacion_id')
            ->whereNull('deleted_at')
            ->exists();

        $aprendiz->update([
            'ficha_caracterizacion_id' => null,
            'estado' => 0,
            'user_edit_id' => Auth::id(),
        ]);

        $rolRemovido = false;
        if (! $tieneOtraFicha && $aprendiz->persona && $aprendiz->persona->user) {
            if ($aprendiz->persona->user->hasRole('APRENDIZ')) {
                $aprendiz->persona->user->removeRole('APRENDIZ');
                $rolRemovido = true;

                Log::info('Rol APRENDIZ removido del usuario al desasignar aprendiz', [
                    'user_id' => $aprendiz->persona->user->id,
                    'persona_id' => $aprendiz->persona_id,
                    'aprendiz_id' => $aprendiz->id,
                    'ficha_id' => $fichaId,
                ]);
            }
        }

        Log::info('Aprendiz desactivado exitosamente', [
            'aprendiz_id' => $aprendiz->id,
            'persona_id' => $aprendiz->persona_id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => 0,
            'ficha_anterior' => $fichaAnterior,
            'ficha_caracterizacion_id_nuevo' => null,
            'tiene_otra_ficha' => $tieneOtraFicha,
            'user_id' => Auth::id(),
        ]);

        return [
            'aprendiz_id' => $aprendiz->id,
            'persona_id' => $aprendiz->persona_id,
            'estado_anterior' => $estadoAnterior,
            'ficha_anterior' => $fichaAnterior,
            'rol_removido' => $rolRemovido,
        ];
    }
}
