<?php

namespace App\Services;

use App\Repositories\InstructorFichaCaracterizacionRepository;
use App\Repositories\InstructorRepository;
use App\Repositories\ParametroRepository;
use App\Repositories\PersonaRepository;
use App\Services\Concerns\AsistenceQr\HandlesAsistenceQrCaracterizacionActions;
use App\Services\Concerns\AsistenceQr\HandlesAsistenceQrInstructorActions;

class AsistenceQrService
{
    use HandlesAsistenceQrCaracterizacionActions;
    use HandlesAsistenceQrInstructorActions;

    protected InstructorFichaCaracterizacionRepository $instructorFichaCaracterizacionRepository;

    protected InstructorRepository $instructorRepository;

    protected PersonaRepository $personaRepository;

    protected ParametroRepository $parametroRepository;

    public function __construct(
        InstructorFichaCaracterizacionRepository $instructorFichaCaracterizacionRepository,
        InstructorRepository $instructorRepository,
        PersonaRepository $personaRepository,
        ParametroRepository $parametroRepository
    ) {
        $this->instructorFichaCaracterizacionRepository = $instructorFichaCaracterizacionRepository;
        $this->instructorRepository = $instructorRepository;
        $this->personaRepository = $personaRepository;
        $this->parametroRepository = $parametroRepository;
    }
}
