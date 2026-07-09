<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    use HasCache;

    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['usuarios', 'auth'];
    }

    /**
     * Encuentra usuario por email
     */
    public function encontrarPorEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Encuentra usuario por persona
     */
    public function encontrarPorPersona(int $personaId): ?User
    {
        return User::where('persona_id', $personaId)
            ->with('persona')
            ->first();
    }

    /**
     * Obtiene usuarios por rol
     */
    public function obtenerPorRol(string $rol): Collection
    {
        return $this->cache("rol.{$rol}.usuarios", function () use ($rol) {
            return User::role($rol)
                ->with('persona')
                ->get();
        }, 60); // 1 hora
    }

    /**
     * Crea un nuevo usuario
     */
    public function crear(array $datos): User
    {
        $user = User::create($datos);
        $this->invalidarCache();

        return $user;
    }

    /**
     * Actualiza usuario
     */
    public function actualizar(int $id, array $datos): bool
    {
        $actualizado = User::where('id', $id)->update($datos);
        $this->invalidarCache();

        return $actualizado;
    }

    /**
     * Invalida caché
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}
