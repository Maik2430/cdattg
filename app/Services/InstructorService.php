<?php

namespace App\Services;

use App\Repositories\InstructorRepository;
use App\Services\Concerns\Instructor\HandlesInstructorActualizacionActions;
use App\Services\Concerns\Instructor\HandlesInstructorConsultaActions;
use App\Services\Concerns\Instructor\HandlesInstructorCreacionActions;
use App\Services\Concerns\Instructor\HandlesInstructorCreacionRelacionesHelpers;
use App\Services\Concerns\Instructor\HandlesInstructorEliminacionActions;
use App\Services\Concerns\Instructor\HandlesInstructorEspecialidadesHelpers;
use App\Services\Concerns\Instructor\HandlesInstructorEstadoActions;
use App\Services\Concerns\Instructor\HandlesInstructorListadoActions;
use App\Services\Concerns\Instructor\HandlesInstructorListadoQueryHelpers;

class InstructorService
{
    use HandlesInstructorActualizacionActions;
    use HandlesInstructorConsultaActions;
    use HandlesInstructorCreacionActions;
    use HandlesInstructorCreacionRelacionesHelpers;
    use HandlesInstructorEliminacionActions;
    use HandlesInstructorEspecialidadesHelpers;
    use HandlesInstructorEstadoActions;
    use HandlesInstructorListadoActions;
    use HandlesInstructorListadoQueryHelpers;

    protected InstructorRepository $repository;

    public function __construct(InstructorRepository $repository)
    {
        $this->repository = $repository;
    }
}
