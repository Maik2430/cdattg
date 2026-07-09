<?php

namespace App\Services;

use App\Repositories\AsignacionInstructorLogRepository;
use App\Repositories\LoginRepository;
use App\Repositories\SenasofiaplusValidationLogRepository;
use App\Services\Concerns\Auditoria\HandlesAuditoriaConsultaActions;
use App\Services\Concerns\Auditoria\HandlesAuditoriaRegistroActions;

class AuditoriaService
{
    use HandlesAuditoriaConsultaActions;
    use HandlesAuditoriaRegistroActions;

    protected LoginRepository $loginRepo;

    protected AsignacionInstructorLogRepository $asignacionLogRepo;

    protected SenasofiaplusValidationLogRepository $senasofiaplusLogRepo;

    public function __construct(
        LoginRepository $loginRepo,
        AsignacionInstructorLogRepository $asignacionLogRepo,
        SenasofiaplusValidationLogRepository $senasofiaplusLogRepo
    ) {
        $this->loginRepo = $loginRepo;
        $this->asignacionLogRepo = $asignacionLogRepo;
        $this->senasofiaplusLogRepo = $senasofiaplusLogRepo;
    }
}
