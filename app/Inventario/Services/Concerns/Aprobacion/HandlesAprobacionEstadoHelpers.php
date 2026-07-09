<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Aprobacion;

use App\Exceptions\AprobacionException;
use App\Models\ParametroTema;

trait HandlesAprobacionEstadoHelpers
{
    public function obtenerEstadoEnEspera(): ?ParametroTema
    {
        return $this->obtenerEstadoPorNombre(self::STATUS_PENDING);
    }

    /**
     * @throws AprobacionException
     */
    public function obtenerEstadoAprobada(): ParametroTema
    {
        $parametroTema = $this->obtenerEstadoPorNombre(self::STATUS_APPROVED);
        if (! $parametroTema) {
            throw new AprobacionException("Estado '".self::STATUS_APPROVED."' no encontrado en '".self::ORDER_STATUS_THEME."'.");
        }

        return $parametroTema;
    }

    /**
     * @throws AprobacionException
     */
    public function obtenerEstadoRechazada(): ParametroTema
    {
        $parametroTema = $this->obtenerEstadoPorNombre(self::STATUS_REJECTED);
        if (! $parametroTema) {
            throw new AprobacionException("Estado '".self::STATUS_REJECTED."' no encontrado en '".self::ORDER_STATUS_THEME."'.");
        }

        return $parametroTema;
    }

    /**
     * Obtiene un estado por nombre en el tema de estados de orden.
     */
    private function obtenerEstadoPorNombre(string $name): ?ParametroTema
    {
        return $this->formOptionsService->obtenerEstadoOrdenPorNombre($name, self::ORDER_STATUS_THEME);
    }
}
