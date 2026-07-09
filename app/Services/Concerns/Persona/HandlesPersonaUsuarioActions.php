<?php

namespace App\Services\Concerns\Persona;

use App\Exceptions\PersonaException;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaUsuarioActions
{
    /**
     * Cambia estado del usuario de una persona
     *
     * @throws PersonaException
     */
    public function cambiarEstadoUsuario(int $personaId): bool
    {
        $persona = $this->obtener($personaId);

        if (! $persona || ! $persona->user) {
            throw new PersonaException('Persona o usuario no encontrado');
        }

        $nuevoEstado = ! $persona->user->status;

        return $this->userRepo->actualizar($persona->user->id, [
            'status' => $nuevoEstado,
        ]);
    }

    /**
     * Crea un usuario asociado a una persona existente.
     *
     * @throws PersonaException
     */
    public function crearUsuarioParaPersona(Persona $persona): User
    {
        if ($persona->user) {
            throw new PersonaException('La persona ya tiene un usuario asociado.');
        }

        if (empty($persona->email)) {
            throw new PersonaException('La persona no tiene correo registrado.');
        }

        if (empty($persona->numero_documento)) {
            throw new PersonaException('La persona no tiene número de documento registrado.');
        }

        return DB::transaction(function () use ($persona) {
            return $this->crearUsuarioPersona($persona);
        });
    }

    /**
     * Crea usuario asociado a persona
     */
    protected function crearUsuarioPersona(Persona $persona): User
    {
        $user = $this->userRepo->crear([
            'email' => $persona->email,
            'password' => Hash::make($persona->numero_documento),
            'persona_id' => $persona->id,
            'status' => true,
        ]);

        $user->assignRole('VISITANTE');

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            Log::warning('No se pudo enviar email de verificación al crear usuario', [
                'user_id' => $user->id,
                'persona_id' => $persona->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $user;
    }
}
