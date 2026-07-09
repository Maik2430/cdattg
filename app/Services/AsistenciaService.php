<?php

namespace App\Services;

use App\Repositories\AsistenciaAprendizRepository;
use App\Services\Concerns\Asistencia\HandlesAsistenciaNovedadActions;
use App\Services\Concerns\Asistencia\HandlesAsistenciaReadActions;
use App\Services\Concerns\Asistencia\HandlesAsistenciaRegistroActions;

class AsistenciaService
{
    use HandlesAsistenciaNovedadActions;
    use HandlesAsistenciaReadActions;
    use HandlesAsistenciaRegistroActions;

    protected AsistenciaAprendizRepository $repository;

    protected JornadaValidationService $jornadaValidation;

    public function __construct(
        AsistenciaAprendizRepository $repository,
        JornadaValidationService $jornadaValidation
    ) {
        $this->repository = $repository;
        $this->jornadaValidation = $jornadaValidation;
    }
}
