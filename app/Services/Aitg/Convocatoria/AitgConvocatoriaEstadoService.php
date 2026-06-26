<?php

namespace App\Services\Aitg\Convocatoria;

use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\Convocatoria\Convocatoria;
use Illuminate\Support\Collection;

/** Transiciones automáticas de estado de convocatorias. */
class AitgConvocatoriaEstadoService
{
    public function sincronizarEstadosAutomaticos(?Collection $convocatorias = null): void
    {
        $query = Convocatoria::query()->where('estado', 'publicada');

        if ($convocatorias !== null) {
            $query->whereIn('id', $convocatorias->pluck('id'));
        }

        $hoy = now()->startOfDay();

        $query->whereNotNull('fecha_fin_publicacion')
            ->whereDate('fecha_fin_publicacion', '<', $hoy)
            ->update(['estado' => 'cerrada', 'updated_at' => now()]);
    }

    public function finalizarConSeleccion(Convocatoria $convocatoria, PostulacionPlan $postulacion): Convocatoria
    {
        abort_unless($postulacion->convocatoria_id === $convocatoria->id, 422);

        $convocatoria->update([
            'estado' => 'finalizada',
            'postulacion_seleccionada_id' => $postulacion->id,
        ]);

        return $convocatoria->fresh(['postulacionSeleccionada.user.persona', 'postulacionSeleccionada.perfilPlan']);
    }
}
