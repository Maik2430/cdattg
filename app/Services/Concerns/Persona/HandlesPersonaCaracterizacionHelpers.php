<?php

namespace App\Services\Concerns\Persona;

use App\Models\Persona;

trait HandlesPersonaCaracterizacionHelpers
{
    /**
     * @param  array<string,mixed>  $datos
     * @return array<int,int>
     */
    private function extraerCaracterizacionIds(array &$datos): array
    {
        $ids = collect($datos['caracterizacion_ids'] ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        unset($datos['caracterizacion_ids']);

        return $ids;
    }

    /**
     * @param  array<int,int>  $caracterizacionesIds
     */
    private function syncCaracterizaciones(Persona $persona, array $caracterizacionesIds): void
    {
        $persona->caracterizacionesComplementarias()->sync($caracterizacionesIds);

        if (! empty($caracterizacionesIds)) {
            $persona->update(['parametro_id' => $caracterizacionesIds[0]]);
        } else {
            $persona->update(['parametro_id' => null]);
        }
    }
}
