<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Repositories\Concerns\Ficha\HandlesFichaRepositoryQueryActions;
use App\Repositories\Concerns\Ficha\HandlesFichaRepositoryWriteActions;

class FichaRepository
{
    use HandlesFichaRepositoryQueryActions;
    use HandlesFichaRepositoryWriteActions;
    use HasCache;

    public function __construct()
    {
        $this->cacheType = 'fichas';
        $this->cacheTags = ['fichas', 'caracterizacion'];
    }
}
