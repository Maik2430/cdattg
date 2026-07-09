<?php

namespace App\Services;

use App\Services\Concerns\InstructorBusinessRules\HandlesInstructorBusinessRulesCargaHorariaHelpers;
use App\Services\Concerns\InstructorBusinessRules\HandlesInstructorBusinessRulesConstants;
use App\Services\Concerns\InstructorBusinessRules\HandlesInstructorBusinessRulesDisponibilidadActions;
use App\Services\Concerns\InstructorBusinessRules\HandlesInstructorBusinessRulesDisponibilidadEvaluacionHelpers;
use App\Services\Concerns\InstructorBusinessRules\HandlesInstructorBusinessRulesDisponiblesActions;
use App\Services\Concerns\InstructorBusinessRules\HandlesInstructorBusinessRulesEspecialidadHelpers;
use App\Services\Concerns\InstructorBusinessRules\HandlesInstructorBusinessRulesEstadisticasActions;
use App\Services\Concerns\InstructorBusinessRules\HandlesInstructorBusinessRulesFichasContadoresHelpers;
use App\Services\Concerns\InstructorBusinessRules\HandlesInstructorBusinessRulesReglasSenaActions;
use App\Services\Concerns\InstructorBusinessRules\HandlesInstructorBusinessRulesSuperposicionHelpers;

class InstructorBusinessRulesService
{
    use HandlesInstructorBusinessRulesCargaHorariaHelpers;
    use HandlesInstructorBusinessRulesConstants;
    use HandlesInstructorBusinessRulesDisponibilidadActions;
    use HandlesInstructorBusinessRulesDisponibilidadEvaluacionHelpers;
    use HandlesInstructorBusinessRulesDisponiblesActions;
    use HandlesInstructorBusinessRulesEspecialidadHelpers;
    use HandlesInstructorBusinessRulesEstadisticasActions;
    use HandlesInstructorBusinessRulesFichasContadoresHelpers;
    use HandlesInstructorBusinessRulesReglasSenaActions;
    use HandlesInstructorBusinessRulesSuperposicionHelpers;

    /**
     * Límite máximo de fichas activas por instructor según reglas SENA
     */
    const MAX_FICHAS_ACTIVAS = 5;

    /**
     * Experiencia mínima requerida para instructores
     */
    const EXPERIENCIA_MINIMA = 1;

    /**
     * Horas máximas por semana para un instructor
     */
    const MAX_HORAS_SEMANA = 48;
}
