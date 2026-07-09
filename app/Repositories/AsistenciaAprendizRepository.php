<?php

namespace App\Repositories;

use App\Repositories\Concerns\AsistenciaAprendiz\HandlesAsistenciaAprendizQueryActions;
use App\Repositories\Concerns\AsistenciaAprendiz\HandlesAsistenciaAprendizWriteActions;

class AsistenciaAprendizRepository
{
    use HandlesAsistenciaAprendizQueryActions;
    use HandlesAsistenciaAprendizWriteActions;
}
