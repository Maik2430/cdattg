<?php

namespace App\Services\Concerns\Persona;

use App\Models\Persona;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait HandlesPersonaReadActions
{
    /**
     * Lista personas paginadas
     */
    public function listar(int $perPage = 10): LengthAwarePaginator
    {
        return Persona::with(['tipoDocumento', 'tipoGenero', 'user'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtiene persona con relaciones
     */
    public function obtener(int $id): ?Persona
    {
        return Persona::with(['tipoDocumento', 'tipoGenero', 'user', 'aprendiz', 'instructor'])
            ->find($id);
    }

    /**
     * Busca una persona por número de documento
     */
    public function buscarPorDocumento(string $numeroDocumento): ?Persona
    {
        return Persona::with([
            'tipoDocumento',
            'tipoGenero',
            'pais',
            'departamento',
            'municipio',
            'caracterizacionesComplementarias',
        ])->where('numero_documento', $numeroDocumento)->first();
    }
}
