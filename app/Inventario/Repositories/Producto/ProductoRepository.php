<?php

declare(strict_types=1);

namespace App\Inventario\Repositories\Producto;

use App\Inventario\Interfaces\Repositories\Producto\ProductoRepositoryInterface;
use App\Inventario\Repositories\Concerns\Producto\HandlesProductoRepositoryCatalogQueryActions;
use App\Inventario\Repositories\Concerns\Producto\HandlesProductoRepositoryCrudActions;
use App\Inventario\Repositories\Concerns\Producto\HandlesProductoRepositoryFilterQueryActions;

class ProductoRepository implements ProductoRepositoryInterface
{
    use HandlesProductoRepositoryCatalogQueryActions;
    use HandlesProductoRepositoryCrudActions;
    use HandlesProductoRepositoryFilterQueryActions;
}
