<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ParametroTema;
use App\Repositories\Interfaces\ParametroTemaRepositoryInterface;

class ParametroTemaRepository implements ParametroTemaRepositoryInterface
{
    /**
     * Busca un parámetro por tema y nombre exacto
     *
     * @param string $temaNombre
     * @param string $parametroNombre
     * @return ParametroTema|null
     */
    public function buscarPorTemaYNombre(string $temaNombre, string $parametroNombre): ?ParametroTema
    {
        return ParametroTema::whereHas('tema', function ($q) use ($temaNombre) {
            $q->where('name', $temaNombre);
        })
        ->whereHas('parametro', function ($q) use ($parametroNombre) {
            $q->where('name', $parametroNombre);
        })
        ->first();
    }

    /**
     * Busca un parámetro por tema y nombre normalizado (sin acentos)
     *
     * @param string $temaNombre
     * @param string $codigo
     * @return ParametroTema|null
     */
    public function buscarPorTemaYNombreNormalizado(string $temaNombre, string $codigo): ?ParametroTema
    {
        $nombreNormalizado = str_replace(
            ['Á', 'É', 'Í', 'Ó', 'Ú'],
            ['A', 'E', 'I', 'O', 'U'],
            strtoupper($codigo)
        );

        return ParametroTema::whereHas('tema', function ($q) use ($temaNombre) {
            $q->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(name, "Á", "A"), "É", "E"), "Í", "I"), "Ó", "O"), "Ú", "U")) = ?', [$temaNombre]);
        })
        ->whereHas('parametro', function ($q) use ($nombreNormalizado) {
            $q->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(name, "Á", "A"), "É", "E"), "Í", "I"), "Ó", "O"), "Ú", "U")) = ?', [$nombreNormalizado]);
        })
        ->first();
    }

    /**
     * Obtiene todos los parámetros de un tema
     *
     * @param string $temaNombre
     * @return array
     */
    public function obtenerPorTema(string $temaNombre): array
    {
        return ParametroTema::with(['parametro', 'tema'])
            ->whereHas('tema', function ($q) use ($temaNombre) {
                $q->where('name', $temaNombre);
            })
            ->where('status', 1)
            ->get()
            ->toArray();
    }
}

