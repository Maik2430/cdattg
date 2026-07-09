<?php

declare(strict_types=1);

namespace App\Inventario\Services\Orden;

use App\Inventario\Interfaces\Repositories\Orden\DetalleOrdenRepositoryInterface;
use App\Inventario\Interfaces\Repositories\Orden\OrdenRepositoryInterface;
use App\Inventario\Interfaces\Repositories\ParametroTema\ParametroTemaRepositoryInterface;
use App\Inventario\Interfaces\Repositories\Producto\ProductoRepositoryInterface;
use App\Inventario\Interfaces\Services\NotificationServiceInterface;
use App\Inventario\Interfaces\Services\StockValidatorServiceInterface;
use App\Inventario\Interfaces\Services\TransactionServiceInterface;
use App\Inventario\Services\Concerns\Orden\DefinesOrdenConstants;
use App\Inventario\Services\Concerns\Orden\HandlesOrdenActualizacionActions;
use App\Inventario\Services\Concerns\Orden\HandlesOrdenCreacionActions;
use App\Inventario\Services\Concerns\Orden\HandlesOrdenDescripcionHelpers;
use App\Inventario\Services\Concerns\Orden\HandlesOrdenDetalleHelpers;
use App\Inventario\Services\Concerns\Orden\HandlesOrdenEstadoHelpers;
use App\Inventario\Services\Concerns\Orden\HandlesOrdenHistorialActions;
use App\Inventario\Services\Concerns\Orden\HandlesOrdenNotificacionHelpers;

class OrdenService
{
    use DefinesOrdenConstants;
    use HandlesOrdenActualizacionActions;
    use HandlesOrdenCreacionActions;
    use HandlesOrdenDescripcionHelpers;
    use HandlesOrdenDetalleHelpers;
    use HandlesOrdenEstadoHelpers;
    use HandlesOrdenHistorialActions;
    use HandlesOrdenNotificacionHelpers;

    public function __construct(
        protected OrdenRepositoryInterface $ordenRepository,
        protected DetalleOrdenRepositoryInterface $detalleOrdenRepository,
        protected ProductoRepositoryInterface $productoRepository,
        protected ParametroTemaRepositoryInterface $parametroTemaRepository,
        protected NotificationServiceInterface $notificationService,
        protected TransactionServiceInterface $transactionService,
        protected StockValidatorServiceInterface $stockValidator,
    ) {}
}
