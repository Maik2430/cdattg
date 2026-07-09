<?php

namespace App\Repositories\Concerns\Persona;

use App\Models\Persona;

trait HandlesPersonaRepositoryCaracterizacionHelpers
{
    private function extraerCaracterizacionIds(array &$data): array
    {
        $ids = collect($data['caracterizacion_ids'] ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        unset($data['caracterizacion_ids']);

        return $ids;
    }

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
