<?php

declare(strict_types=1);

namespace App\Inventario\Services\Aprobacion;

use App\Inventario\Interfaces\Repositories\Aprobacion\AprobacionRepositoryInterface;
use App\Inventario\Interfaces\Repositories\Orden\DetalleOrdenRepositoryInterface;
use App\Inventario\Interfaces\Repositories\Orden\OrdenRepositoryInterface;
use App\Inventario\Interfaces\Repositories\Producto\ProductoRepositoryInterface;
use App\Inventario\Interfaces\Services\FormOptionsServiceInterface;
use App\Inventario\Interfaces\Services\StockValidatorServiceInterface;
use App\Inventario\Interfaces\Services\TransactionServiceInterface;
use App\Inventario\Services\Concerns\Aprobacion\DefinesAprobacionConstants;
use App\Inventario\Services\Concerns\Aprobacion\HandlesAprobacionConsultaActions;
use App\Inventario\Services\Concerns\Aprobacion\HandlesAprobacionDetalleAprobarActions;
use App\Inventario\Services\Concerns\Aprobacion\HandlesAprobacionDetalleRechazarActions;
use App\Inventario\Services\Concerns\Aprobacion\HandlesAprobacionEstadoHelpers;
use App\Inventario\Services\Concerns\Aprobacion\HandlesAprobacionNotificacionHelpers;
use App\Inventario\Services\Concerns\Aprobacion\HandlesAprobacionOrdenCompletaActions;

class AprobacionService
{
    use DefinesAprobacionConstants;
    use HandlesAprobacionConsultaActions;
    use HandlesAprobacionDetalleAprobarActions;
    use HandlesAprobacionDetalleRechazarActions;
    use HandlesAprobacionEstadoHelpers;
    use HandlesAprobacionNotificacionHelpers;
    use HandlesAprobacionOrdenCompletaActions;

    public function __construct(
        protected AprobacionRepositoryInterface $repository,
        protected DetalleOrdenRepositoryInterface $detalleOrdenRepository,
        protected OrdenRepositoryInterface $ordenRepository,
        protected ProductoRepositoryInterface $productoRepository,
        protected TransactionServiceInterface $transactionService,
        protected StockValidatorServiceInterface $stockValidator,
        protected FormOptionsServiceInterface $formOptionsService,
    ) {}
}
