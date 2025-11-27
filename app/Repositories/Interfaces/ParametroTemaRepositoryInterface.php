<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ParametroTema;

interface ParametroTemaRepositoryInterface
{
    public function buscarPorTemaYNombre(string $temaNombre, string $parametroNombre): ?ParametroTema;
    public function buscarPorTemaYNombreNormalizado(string $temaNombre, string $codigo): ?ParametroTema;
    public function obtenerPorTema(string $temaNombre): array;
}

