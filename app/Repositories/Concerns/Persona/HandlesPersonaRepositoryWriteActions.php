<?php

namespace App\Repositories\Concerns\Persona;

use App\Models\Persona;

trait HandlesPersonaRepositoryWriteActions
{
    public function create(array $data): Persona
    {
        $caracterizacionesIds = $this->extraerCaracterizacionIds($data);

        $data = array_merge($data, [
            'user_create_id' => auth()->id() ?? 1,
            'user_edit_id' => auth()->id() ?? 1,
        ]);

        $persona = Persona::create($data);

        if (! empty($caracterizacionesIds)) {
            $this->syncCaracterizaciones($persona, $caracterizacionesIds);
        }

        return $persona;
    }

    public function update(Persona $persona, array $data): bool
    {
        $caracterizacionesIds = $this->extraerCaracterizacionIds($data);

        $data['user_edit_id'] = auth()->id() ?? 1;

        $updated = $persona->update($data);

        if (! empty($caracterizacionesIds)) {
            $this->syncCaracterizaciones($persona, $caracterizacionesIds);
        }

        return $updated;
    }

    public function createOrUpdate(array $data): Persona
    {
        $persona = $this->findByDocumentoOrEmail(
            $data['numero_documento'],
            $data['email']
        );

        $caracterizacionesIds = $this->extraerCaracterizacionIds($data);

        if ($persona) {
            $this->update($persona, $data);
            $this->syncCaracterizaciones($persona, $caracterizacionesIds);

            return $persona->fresh(['caracterizacionesComplementarias', 'caracterizacion']);
        }

        $persona = $this->create($data);
        $this->syncCaracterizaciones($persona, $caracterizacionesIds);

        return $persona->fresh(['caracterizacionesComplementarias', 'caracterizacion']);
    }
}
