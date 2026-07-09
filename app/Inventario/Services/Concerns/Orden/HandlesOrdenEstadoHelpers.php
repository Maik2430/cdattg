<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Orden;

use App\Exceptions\OrdenException;
use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;

trait HandlesOrdenEstadoHelpers
{
    /**
     * Obtiene el tipo de orden como ParametroTema válido
     *
     * @throws OrdenException
     */
    public function obtenerParametroTipoOrden(string $codigo): ParametroTema
    {
        $tema = Tema::where('name', 'TIPOS DE ORDEN')->first();
        if (! $tema) {
            throw new OrdenException("Tema 'TIPOS DE ORDEN' no encontrado.");
        }

        $codigoNormalizado = strtoupper($codigo);
        $codigoNormalizado = str_replace(['É', 'Í', 'Ó'], ['E', 'I', 'O'], $codigoNormalizado);

        $parametro = $tema->parametros()
            ->where('name', $codigoNormalizado)
            ->wherePivot('status', 1)
            ->first();

        if (! $parametro) {
            throw new OrdenException("Tipo de orden '{$codigo}' no encontrado. Verifique los parámetros del sistema.");
        }

        $parametroTema = $this->parametroTemaRepository->obtenerPorTemaYParametro($tema->id, $parametro->id);

        if (! $parametroTema) {
            throw new OrdenException("Tipo de orden '{$codigo}' no está asociado correctamente al tema 'TIPOS DE ORDEN'.");
        }

        return $parametroTema;
    }

    /**
     * Obtiene estado EN ESPERA
     *
     * @return ParametroTema
     *
     * @throws OrdenException
     */
    public function obtenerEstadoEnEspera()
    {
        $parametroTema = $this->parametroTemaRepository->obtenerEstadoPorNombre(self::STATUS_EN_ESPERA, self::THEME_ORDER_STATES);

        if (! $parametroTema) {
            throw new OrdenException("Estado 'EN ESPERA' no encontrado en 'ESTADOS DE ORDEN'. Verifique los parámetros del sistema.");
        }

        return $parametroTema;
    }

    /**
     * Obtiene estado APROBADA
     *
     * @return Parametro
     *
     * @throws OrdenException
     */
    public function obtenerEstadoAprobada()
    {
        $tema = Tema::where('name', self::THEME_ORDER_STATES)->first();
        if (! $tema) {
            throw new OrdenException("Tema 'ESTADOS DE ORDEN' no encontrado.");
        }

        $estado = $tema->parametros()
            ->where('name', self::STATUS_APROBADA)
            ->wherePivot('status', 1)
            ->first();

        if (! $estado) {
            throw new OrdenException("Estado 'APROBADA' no encontrado.");
        }

        return $estado;
    }
}
