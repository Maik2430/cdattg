<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\CompetenciaPrograma;
use Illuminate\Database\Eloquent\Collection;

class CompetenciaProgramaRepository
{
    use HasCache;

    public function __construct()
    {
        $this->cacheType = 'programas';
        $this->cacheTags = ['competencias_programa', 'programas'];
    }

    /**
     * Obtiene competencias por programa
     */
    public function obtenerPorPrograma(int $programaId): Collection
    {
        return $this->cache("programa.{$programaId}.competencias", function () use ($programaId) {
            return CompetenciaPrograma::where('programa_formacion_id', $programaId)
                ->with(['competencia'])
                ->orderBy('orden')
                ->get();
        }, 360); // 6 horas
    }

    /**
     * Crea relación competencia-programa
     */
    public function crear(array $datos): CompetenciaPrograma
    {
        $relacion = CompetenciaPrograma::create($datos);
        $this->invalidarCache();

        return $relacion;
    }

    /**
     * Invalida caché
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}
