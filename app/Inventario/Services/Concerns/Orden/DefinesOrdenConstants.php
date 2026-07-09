<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Orden;

trait DefinesOrdenConstants
{
    private const THEME_ORDER_STATES = 'ESTADOS DE ORDEN';

    private const STATUS_EN_ESPERA = 'EN ESPERA';

    private const STATUS_APROBADA = 'APROBADA';
}
