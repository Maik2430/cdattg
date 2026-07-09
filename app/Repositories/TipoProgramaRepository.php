<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\TipoPrograma;
use Illuminate\Database\Eloquent\Collection;

class TipoProgramaRepository
{
    use HasCache;

    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['tipos_programa', 'configuracion'];
    }

    /**
     * Obtiene todos los tipos de programa activos
     */
    public function obtenerActivos(): Collection
    {
        return $this->cache('activos', function () {
            return TipoPrograma::where('status', true)
                ->orderBy('nombre')
                ->get();
        }, 1440); // 24 horas
    }

    /**
     * Invalida caché
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}
