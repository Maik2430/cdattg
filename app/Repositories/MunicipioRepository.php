<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Municipio;
use Illuminate\Database\Eloquent\Collection;

class MunicipioRepository
{
    use HasCache;

    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['municipios', 'ubicacion'];
    }

    /**
     * Obtiene municipios por departamento
     */
    public function obtenerPorDepartamento(int $departamentoId): Collection
    {
        return $this->cache("departamento.{$departamentoId}.municipios", function () use ($departamentoId) {
            return Municipio::where('departamento_id', $departamentoId)
                ->orderBy('municipio')
                ->get();
        }, 1440); // 24 horas
    }

    /**
     * Busca municipios por nombre
     */
    public function buscar(string $termino, ?int $departamentoId = null): Collection
    {
        $query = Municipio::where('municipio', 'LIKE', "%{$termino}%");

        if ($departamentoId) {
            $query->where('departamento_id', $departamentoId);
        }

        return $query->orderBy('municipio')->limit(20)->get();
    }

    /**
     * Invalida caché
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}
