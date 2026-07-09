<?php

namespace App\Services;

use App\Repositories\AprendizRepository;
use App\Services\Concerns\Aprendiz\HandlesAprendizActualizarActions;
use App\Services\Concerns\Aprendiz\HandlesAprendizCrearActions;
use App\Services\Concerns\Aprendiz\HandlesAprendizEliminarActions;
use App\Services\Concerns\Aprendiz\HandlesAprendizEstadoActions;
use App\Services\Concerns\Aprendiz\HandlesAprendizFormateoActions;
use App\Services\Concerns\Aprendiz\HandlesAprendizReadActions;
use App\Services\Concerns\Aprendiz\HandlesAprendizRolHelpers;

class AprendizService
{
    use HandlesAprendizActualizarActions;
    use HandlesAprendizCrearActions;
    use HandlesAprendizEliminarActions;
    use HandlesAprendizEstadoActions;
    use HandlesAprendizFormateoActions;
    use HandlesAprendizReadActions;
    use HandlesAprendizRolHelpers;

    protected AprendizRepository $repository;

    public function __construct(AprendizRepository $repository)
    {
        $this->repository = $repository;
    }
}
