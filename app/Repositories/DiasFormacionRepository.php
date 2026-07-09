<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\DiasFormacion;
use Illuminate\Database\Eloquent\Collection;

class DiasFormacionRepository
{
    use HasCache;

    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['dias_formacion', 'configuracion'];
    }

    /**
     * Obtiene todos los días de formación
     */
    public function obtenerTodos(): Collection
    {
        return $this->cache('todos', function () {
            return DiasFormacion::orderBy('orden')->get();
        }, 1440); // 24 horas
    }

    /**
     * Obtiene días por ficha
     */
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return DiasFormacion::whereHas('fichas', function ($query) use ($fichaId) {
            $query->where('ficha_caracterizacion_id', $fichaId);
        })->orderBy('orden')->get();
    }

    /**
     * Invalida caché
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}
