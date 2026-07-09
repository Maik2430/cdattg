<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Repositories\Concerns\Aprendiz\HandlesAprendizRepositoryQueryActions;
use App\Repositories\Concerns\Aprendiz\HandlesAprendizRepositoryWriteActions;

class AprendizRepository
{
    use HandlesAprendizRepositoryQueryActions;
    use HandlesAprendizRepositoryWriteActions;
    use HasCache;

    public function __construct()
    {
        $this->cacheType = 'aprendices';
        $this->cacheTags = ['aprendices', 'personas'];
    }
}
