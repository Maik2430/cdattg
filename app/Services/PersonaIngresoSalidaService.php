<?php

namespace App\Services;

use App\Services\Concerns\PersonaIngresoSalida\HandlesPersonaIngresoSalidaEstadisticasDentroActions;
use App\Services\Concerns\PersonaIngresoSalida\HandlesPersonaIngresoSalidaEstadisticasPorFechaActions;
use App\Services\Concerns\PersonaIngresoSalida\HandlesPersonaIngresoSalidaEstadisticasPorHoraActions;
use App\Services\Concerns\PersonaIngresoSalida\HandlesPersonaIngresoSalidaEventosRecientesActions;
use App\Services\Concerns\PersonaIngresoSalida\HandlesPersonaIngresoSalidaFechaNavigationActions;
use App\Services\Concerns\PersonaIngresoSalida\HandlesPersonaIngresoSalidaRegistroActions;
use App\Services\Concerns\PersonaIngresoSalida\HandlesPersonaIngresoSalidaSalidasPendientesActions;
use App\Services\Concerns\PersonaIngresoSalida\HandlesPersonaIngresoSalidaTipoPersonaActions;
use App\Services\Concerns\PersonaIngresoSalida\HandlesPersonaIngresoSalidaTipoPersonaDisplayHelpers;

class PersonaIngresoSalidaService
{
    private const COUNT_TOTAL = 'COUNT(*) as total';

    use HandlesPersonaIngresoSalidaEstadisticasDentroActions;
    use HandlesPersonaIngresoSalidaEstadisticasPorFechaActions;
    use HandlesPersonaIngresoSalidaEstadisticasPorHoraActions;
    use HandlesPersonaIngresoSalidaEventosRecientesActions;
    use HandlesPersonaIngresoSalidaFechaNavigationActions;
    use HandlesPersonaIngresoSalidaRegistroActions;
    use HandlesPersonaIngresoSalidaSalidasPendientesActions;
    use HandlesPersonaIngresoSalidaTipoPersonaActions;
    use HandlesPersonaIngresoSalidaTipoPersonaDisplayHelpers;
}
