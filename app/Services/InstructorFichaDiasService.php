<?php

namespace App\Services;

use App\Services\Concerns\InstructorFichaDias\HandlesInstructorFichaDiasAsignarActions;
use App\Services\Concerns\InstructorFichaDias\HandlesInstructorFichaDiasDiaHelpers;
use App\Services\Concerns\InstructorFichaDias\HandlesInstructorFichaDiasDisponibilidadActions;
use App\Services\Concerns\InstructorFichaDias\HandlesInstructorFichaDiasFechasHelpers;
use App\Services\Concerns\InstructorFichaDias\HandlesInstructorFichaDiasHorasHelpers;
use App\Services\Concerns\InstructorFichaDias\HandlesInstructorFichaDiasReadActions;

class InstructorFichaDiasService
{
    use HandlesInstructorFichaDiasAsignarActions;
    use HandlesInstructorFichaDiasDiaHelpers;
    use HandlesInstructorFichaDiasDisponibilidadActions;
    use HandlesInstructorFichaDiasFechasHelpers;
    use HandlesInstructorFichaDiasHorasHelpers;
    use HandlesInstructorFichaDiasReadActions;
}
