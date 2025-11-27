<?php

declare(strict_types=1);

namespace App\Services\Inventario;

use App\Repositories\Interfaces\ParametroTemaRepositoryInterface;
use App\Exceptions\OrdenException;

class DevolucionesServices
{
    private const THEME_ORDER_STATES = 'ESTADOS DE ORDEN';
    private const STATUS_APROBADA = 'APROBADA';

    protected ParametroTemaRepositoryInterface $parametroTemaRepository;

    public function __construct(ParametroTemaRepositoryInterface $parametroTemaRepository)
    {
        $this->parametroTemaRepository = $parametroTemaRepository;
    }

    /**
     * Obtiene estado APROBADA
     *
     * @return mixed
     * @throws OrdenException
     */
    public function obtenerEstadoAprobada()
    {
        $estado = $this->parametroTemaRepository->buscarPorTemaYNombre(self::THEME_ORDER_STATES, self::STATUS_APROBADA);

        if (!$estado) {
            throw new OrdenException("Estado 'APROBADA' no encontrado.");
        }

        return $estado;
    }
}

