<?php

namespace App\Services\Concerns\Persona;

use App\Exceptions\PersonaException;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;

trait HandlesPersonaWriteActions
{
    /**
     * Crea una persona y su usuario
     */
    public function crear(array $datos): Persona
    {
        $userId = $this->obtenerIdUsuarioAutenticado();

        return DB::transaction(function () use (&$datos, $userId) {
            $datos['user_create_id'] = $userId;
            $datos['user_edit_id'] = $userId;

            $caracterizacionesIds = $this->extraerCaracterizacionIds($datos);

            $persona = Persona::create($datos);

            $this->syncCaracterizaciones($persona, $caracterizacionesIds);

            if (! empty($persona->email)) {
                $this->crearUsuarioPersona($persona);
            }

            return $persona->fresh(['caracterizacionesComplementarias', 'user']);
        });
    }

    /**
     * Actualiza una persona
     */
    public function actualizar(Persona $persona, array $datos): Persona
    {
        $userId = $this->obtenerIdUsuarioAutenticado();

        return DB::transaction(function () use ($persona, &$datos, $userId) {
            $datos['user_edit_id'] = $userId;

            $caracterizacionesIds = $this->extraerCaracterizacionIds($datos);

            $persona->update($datos);

            $this->syncCaracterizaciones($persona, $caracterizacionesIds);

            if ($persona->user && isset($datos['email'])) {
                $this->userRepo->actualizar($persona->user->id, [
                    'email' => $datos['email'],
                ]);
            }

            return $persona->fresh(['caracterizacionesComplementarias', 'user']);
        });
    }

    /**
     * Elimina una persona
     *
     * @throws PersonaException
     */
    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $persona = Persona::find($id);

            if (! $persona) {
                throw new PersonaException('Persona no encontrada');
            }

            if ($persona->aprendiz || $persona->instructor) {
                throw new PersonaException('No se puede eliminar una persona que es aprendiz o instructor');
            }

            return $persona->delete();
        });
    }
}
