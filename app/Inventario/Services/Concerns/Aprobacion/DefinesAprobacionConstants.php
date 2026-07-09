<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Aprobacion;

trait DefinesAprobacionConstants
{
    private const STATUS_PENDING = 'EN ESPERA';

    private const STATUS_APPROVED = 'APROBADA';

    private const STATUS_REJECTED = 'RECHAZADA';

    private const ORDER_STATUS_THEME = 'ESTADOS DE ORDEN';

    private const ERROR_ESTADO_EN_ESPERA_NO_ENCONTRADO = "Estado 'EN ESPERA' no encontrado.";
}
