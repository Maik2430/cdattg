<?php

namespace App\Repositories\Concerns\Persona;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Collection;

trait HandlesPersonaRepositoryQueryActions
{
    public function findByNumeroDocumento(string $numeroDocumento): ?Persona
    {
        return Persona::where('numero_documento', $numeroDocumento)->first();
    }

    public function findByEmail(string $email): ?Persona
    {
        return Persona::where('email', $email)->first();
    }

    public function findByDocumentoOrEmail(string $numeroDocumento, string $email): ?Persona
    {
        return Persona::where('numero_documento', $numeroDocumento)
            ->orWhere('email', $email)
            ->first();
    }

    public function existsByDocumentoOrEmail(string $numeroDocumento, string $email): bool
    {
        return Persona::where('numero_documento', $numeroDocumento)
            ->orWhere('email', $email)
            ->exists();
    }

    public function getAllWithCaracterizacion(): Collection
    {
        return Persona::with(['caracterizacion', 'tipoDocumento'])->get();
    }

    public function search(array $criteria): Collection
    {
        $query = Persona::query();

        if (isset($criteria['departamento_id'])) {
            $query->where('departamento_id', $criteria['departamento_id']);
        }

        if (isset($criteria['municipio_id'])) {
            $query->where('municipio_id', $criteria['municipio_id']);
        }

        if (isset($criteria['genero'])) {
            $query->where('genero', $criteria['genero']);
        }

        if (isset($criteria['caracterizacion_id'])) {
            $query->where('caracterizacion_id', $criteria['caracterizacion_id']);
        }

        return $query->get();
    }

    public function updateDocumentoStatus(Persona $persona, bool $tieneDocumento): bool
    {
        return $persona->update(['condocumento' => $tieneDocumento ? 1 : 0]);
    }
}
