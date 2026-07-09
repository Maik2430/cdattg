<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Repositories\Concerns\Configuracion\HandlesConfiguracionRepositoryCacheActions;
use App\Repositories\Concerns\Configuracion\HandlesConfiguracionRepositoryQueryActions;

class ConfiguracionRepository
{
    use HandlesConfiguracionRepositoryCacheActions;
    use HandlesConfiguracionRepositoryQueryActions;
    use HasCache;

    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['configuracion', 'sistema'];
    }
}
