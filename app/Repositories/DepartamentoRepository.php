<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Departamento;
use Illuminate\Database\Eloquent\Collection;

class DepartamentoRepository
{
    use HasCache;

    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['departamentos', 'ubicacion'];
    }

    /**
     * Obtiene todos los departamentos
     */
    public function obtenerTodos(): Collection
    {
        return $this->cache('todos', function () {
            return Departamento::select(['id', 'departamento as nombre', 'pais_id', 'status'])
                ->orderBy('departamento')
                ->get();
        }, 1440); // 24 horas
    }

    /**
     * Obtiene departamentos por país
     */
    public function obtenerPorPais(int $paisId): Collection
    {
        return $this->cache("pais.{$paisId}.departamentos", function () use ($paisId) {
            return Departamento::select(['id', 'departamento as nombre', 'pais_id'])
                ->where('pais_id', $paisId)
                ->orderBy('departamento')
                ->get();
        }, 1440);
    }

    /**
     * Busca departamentos por nombre
     */
    public function buscar(string $termino, ?int $paisId = null): Collection
    {
        return Departamento::select(['id', 'departamento as nombre', 'pais_id'])
            ->when($paisId, fn ($query) => $query->where('pais_id', $paisId))
            ->where('departamento', 'LIKE', "%{$termino}%")
            ->orderBy('departamento')
            ->limit(20)
            ->get();
    }

    /**
     * Invalida caché
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}
