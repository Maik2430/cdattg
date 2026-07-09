<?php

namespace App\Services;

use App\Repositories\PersonaRepository;
use App\Repositories\UserRepository;
use App\Services\Concerns\Persona\HandlesPersonaAuthHelpers;
use App\Services\Concerns\Persona\HandlesPersonaCaracterizacionHelpers;
use App\Services\Concerns\Persona\HandlesPersonaImportActions;
use App\Services\Concerns\Persona\HandlesPersonaReadActions;
use App\Services\Concerns\Persona\HandlesPersonaUsuarioActions;
use App\Services\Concerns\Persona\HandlesPersonaWriteActions;

class PersonaService
{
    use HandlesPersonaAuthHelpers;
    use HandlesPersonaCaracterizacionHelpers;
    use HandlesPersonaImportActions;
    use HandlesPersonaReadActions;
    use HandlesPersonaUsuarioActions;
    use HandlesPersonaWriteActions;

    protected PersonaRepository $repository;

    protected UserRepository $userRepo;

    public function __construct(
        PersonaRepository $repository,
        UserRepository $userRepo
    ) {
        $this->repository = $repository;
        $this->userRepo = $userRepo;
    }
}
