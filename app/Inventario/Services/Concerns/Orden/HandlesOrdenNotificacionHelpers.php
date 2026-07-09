<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Orden;

use App\Models\Inventario\Orden;

trait HandlesOrdenNotificacionHelpers
{
    /**
     * Notifica a administradores sobre nueva orden
     */
    private function notificarNuevaOrden(Orden $orden): void
    {
        $this->notificationService->notificarNuevaOrden($orden);
    }
}
