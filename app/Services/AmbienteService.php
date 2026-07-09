<?php

namespace App\Services;

use App\Repositories\AmbienteRepository;
use App\Repositories\RegionalRepository;
use App\Services\Concerns\Ambiente\HandlesAmbienteReadActions;
use App\Services\Concerns\Ambiente\HandlesAmbienteWriteActions;

class AmbienteService
{
    use HandlesAmbienteReadActions;
    use HandlesAmbienteWriteActions;

    protected AmbienteRepository $repository;

    protected RegionalRepository $regionalRepo;

    public function __construct(
        AmbienteRepository $repository,
        RegionalRepository $regionalRepo
    ) {
        $this->repository = $repository;
        $this->regionalRepo = $regionalRepo;
    }
}
