<?php

namespace App\Repositories;

use App\Models\Tema;
use Illuminate\Database\Eloquent\Collection;

class TemaRepository
{
    /**
     * Obtiene todos los temas con parámetros activos
     */
    public function obtenerConParametros(): Collection
    {
        return Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->get();
    }

    /**
     * Obtiene un tema específico con parámetros
     */
    public function encontrarConParametros(int $id): ?Tema
    {
        return Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->find($id);
    }

    /**
     * Obtiene tipos de documento
     */
    public function obtenerTiposDocumento(): ?Tema
    {
        return $this->encontrarConParametros(2);
    }

    /**
     * Obtiene géneros
     */
    public function obtenerGeneros(): ?Tema
    {
        return $this->encontrarConParametros(3);
    }

    /**
     * Obtiene caracterizaciones complementarias
     */
    public function obtenerCaracterizacionesComplementarias(): ?Tema
    {
        return $this->encontrarConParametros(16);
    }

    /**
     * Obtiene vias
     */
    public function obtenerVias(): ?Tema
    {
        return $this->encontrarConParametros(17);
    }

    /**
     * Obtiene letras
     */
    public function obtenerLetras(): ?Tema
    {
        return $this->encontrarConParametros(18);
    }

    /**
     * Obtiene cardinales
     */
    public function obtenerCardinales(): ?Tema
    {
        $tema = $this->encontrarConParametros(18);

        if (! $tema) {
            return null;
        }

        $parametros = $tema->parametros()
            ->wherePivotIn('parametro_id', [250, 259, 260, 264])
            ->orderBy('name')
            ->get();

        $tema->setRelation('parametros', $parametros);

        return $tema;
    }

    /**
     * Obtiene nivel de escolaridad
     */
    public function obtenerNivelEscolaridad(): ?Tema
    {
        return $this->encontrarConParametros(23);
    }
}
