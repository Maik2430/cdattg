<?php

namespace App\Services;

use App\Services\Concerns\JornadaValidation\HandlesJornadaValidationConsultaHelpers;
use App\Services\Concerns\JornadaValidation\HandlesJornadaValidationHorarioActions;
use App\Services\Concerns\JornadaValidation\HandlesJornadaValidationTardanzaActions;

class JornadaValidationService
{
    use HandlesJornadaValidationConsultaHelpers;
    use HandlesJornadaValidationHorarioActions;
    use HandlesJornadaValidationTardanzaActions;
}
