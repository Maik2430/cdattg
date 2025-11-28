<?php

declare(strict_types=1);

namespace App\Inventario\Interfaces\Services;

interface FormOptionsServiceInterface
{
    public function obtenerOpcionesProducto(?string $temaEstados = null): array;
    public function obtenerOpcionesOrden(): array;
    public function obtenerTiposProducto();
    public function obtenerUnidadesMedida();
    public function obtenerEstados(string $tema);
    public function obtenerCategorias();
    public function obtenerMarcas();
    public function obtenerTiposOrden();
    public function obtenerEstadosOrden();
    public function obtenerEstadoAgotado(?string $temaEstados = null);
    public function obtenerEstadoOrdenPorNombre(string $nombreEstado, ?string $temaEstados = null);
}

