<?php

namespace App\Services;

use App\Services\Concerns\AsignacionInstructor\HandlesAsignacionInstructorActualizarHelpers;
use App\Services\Concerns\AsignacionInstructor\HandlesAsignacionInstructorAsignarActions;
use App\Services\Concerns\AsignacionInstructor\HandlesAsignacionInstructorAsignarLogHelpers;
use App\Services\Concerns\AsignacionInstructor\HandlesAsignacionInstructorCrearHelpers;
use App\Services\Concerns\AsignacionInstructor\HandlesAsignacionInstructorDesasignarActions;
use App\Services\Concerns\AsignacionInstructor\HandlesAsignacionInstructorDisponiblesActions;
use App\Services\Concerns\AsignacionInstructor\HandlesAsignacionInstructorDisponiblesQueryHelpers;
use App\Services\Concerns\AsignacionInstructor\HandlesAsignacionInstructorDisponiblesValidationHelpers;
use App\Services\Concerns\AsignacionInstructor\HandlesAsignacionInstructorEstadisticasActions;
use App\Services\Concerns\AsignacionInstructor\HandlesAsignacionInstructorHorasHelpers;
use App\Services\Concerns\AsignacionInstructor\HandlesAsignacionInstructorRolHelpers;

class AsignacionInstructorService
{
    use HandlesAsignacionInstructorActualizarHelpers;
    use HandlesAsignacionInstructorAsignarActions;
    use HandlesAsignacionInstructorAsignarLogHelpers;
    use HandlesAsignacionInstructorCrearHelpers;
    use HandlesAsignacionInstructorDesasignarActions;
    use HandlesAsignacionInstructorDisponiblesActions;
    use HandlesAsignacionInstructorDisponiblesQueryHelpers;
    use HandlesAsignacionInstructorDisponiblesValidationHelpers;
    use HandlesAsignacionInstructorEstadisticasActions;
    use HandlesAsignacionInstructorHorasHelpers;
    use HandlesAsignacionInstructorRolHelpers;

    protected InstructorBusinessRulesService $businessRulesService;

    public function __construct(InstructorBusinessRulesService $businessRulesService)
    {
        $this->businessRulesService = $businessRulesService;
    }
}
