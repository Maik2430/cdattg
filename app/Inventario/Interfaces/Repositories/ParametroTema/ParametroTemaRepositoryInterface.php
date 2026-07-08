<?php

declare(strict_types=1);

namespace App\Inventario\Interfaces\Repositories\ParametroTema;

use App\Models\ParametroTema;
use Illuminate\Support\Collection;

interface ParametroTemaRepositoryInterface
{
    /**
     * Obtiene parámetros tema por nombre de tema
     */
    public function obtenerPorTema(string $nombreTema): Collection;

    /**
     * Obtiene un parámetro tema específico por tema y parámetro
     *
     * @return ParametroTema|null
     */
    public function obtenerPorTemaYParametro(int $temaId, int $parametroId);

    /**
     * Obtiene un estado específico por nombre
     *
     * @return ParametroTema|null
     */
    public function obtenerEstadoPorNombre(string $nombreEstado, string $nombreTema);
}
