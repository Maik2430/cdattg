<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\ProgramaFormacion;
use Illuminate\Database\Eloquent\Collection;

class ProgramaFormacionRepository
{
    use HasCache;

    public function __construct()
    {
        $this->cacheType = 'programas';
        $this->cacheTags = ['programas', 'configuracion'];
    }

    /**
     * Obtiene todos los programas activos con relaciones
     */
    public function obtenerActivos(): Collection
    {
        return $this->cache('activos', function () {
            return ProgramaFormacion::where('status', true)
                ->with(['redConocimiento', 'nivelFormacion', 'tipoPrograma'])
                ->orderBy('nombre')
                ->get();
        }, 360); // 6 horas
    }

    /**
     * Obtiene programas por red de conocimiento
     */
    public function obtenerPorRed(int $redId): Collection
    {
        return $this->cache("red.{$redId}.programas", function () use ($redId) {
            return ProgramaFormacion::where('red_conocimiento_id', $redId)
                ->where('status', true)
                ->orderBy('nombre')
                ->get();
        }, 360);
    }

    /**
     * Busca programas por nombre
     */
    public function buscar(string $termino): Collection
    {
        return ProgramaFormacion::where('nombre', 'LIKE', "%{$termino}%")
            ->where('status', true)
            ->with(['redConocimiento', 'nivelFormacion'])
            ->limit(10)
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
