<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Orden;

use App\Inventario\Interfaces\Repositories\Orden\OrdenRepositoryInterface;
use App\Inventario\Repositories\Concerns\Orden\HandlesOrdenRepositoryCrudActions;
use App\Inventario\Repositories\Concerns\Orden\HandlesOrdenRepositoryQueryActions;

class OrdenRepository implements OrdenRepositoryInterface
{
    use HandlesOrdenRepositoryCrudActions;
    use HandlesOrdenRepositoryQueryActions;
}
