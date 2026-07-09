<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\PersonaIngresoSalida\HandlesPersonaIngresoSalidaEstadisticasActions;
use App\Http\Controllers\Concerns\PersonaIngresoSalida\HandlesPersonaIngresoSalidaRegistroActions;
use App\Services\PersonaIngresoSalidaService;

class PersonaIngresoSalidaController extends Controller
{
    use HandlesPersonaIngresoSalidaEstadisticasActions;
    use HandlesPersonaIngresoSalidaRegistroActions;

    protected PersonaIngresoSalidaService $personaIngresoSalidaService;

    public function __construct(PersonaIngresoSalidaService $personaIngresoSalidaService)
    {
        $this->personaIngresoSalidaService = $personaIngresoSalidaService;
    }
}
