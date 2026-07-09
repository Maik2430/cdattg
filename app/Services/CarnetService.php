<?php

namespace App\Services;

use App\Services\Concerns\Carnet\HandlesCarnetGeneracionActions;
use App\Services\Concerns\Carnet\HandlesCarnetPlantillaHelpers;
use App\Services\Concerns\Carnet\HandlesCarnetVerificacionActions;

class CarnetService
{
    use HandlesCarnetGeneracionActions;
    use HandlesCarnetPlantillaHelpers;
    use HandlesCarnetVerificacionActions;
}
