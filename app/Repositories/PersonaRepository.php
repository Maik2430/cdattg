<?php

namespace App\Repositories;

use App\Repositories\Concerns\Persona\HandlesPersonaRepositoryCaracterizacionHelpers;
use App\Repositories\Concerns\Persona\HandlesPersonaRepositoryQueryActions;
use App\Repositories\Concerns\Persona\HandlesPersonaRepositoryStatisticsActions;
use App\Repositories\Concerns\Persona\HandlesPersonaRepositoryWriteActions;

class PersonaRepository
{
    use HandlesPersonaRepositoryCaracterizacionHelpers;
    use HandlesPersonaRepositoryQueryActions;
    use HandlesPersonaRepositoryStatisticsActions;
    use HandlesPersonaRepositoryWriteActions;
}
