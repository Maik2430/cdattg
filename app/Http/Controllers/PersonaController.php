<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Persona\HandlesPersonaAccessMiddleware;
use App\Http\Controllers\Concerns\Persona\HandlesPersonaApiActions;
use App\Http\Controllers\Concerns\Persona\HandlesPersonaCrudReadActions;
use App\Http\Controllers\Concerns\Persona\HandlesPersonaCrudWriteActions;
use App\Http\Controllers\Concerns\Persona\HandlesPersonaDatatableActions;
use App\Http\Controllers\Concerns\Persona\HandlesPersonaEstadoActions;
use App\Http\Controllers\Concerns\Persona\HandlesPersonaPermissionConstants;
use App\Http\Controllers\Concerns\Persona\HandlesPersonaUserRoleActions;
use App\Repositories\TemaRepository;
use App\Services\PersonaService;
use App\Services\UbicacionService;

class PersonaController extends Controller
{
    use HandlesPersonaAccessMiddleware;
    use HandlesPersonaApiActions;
    use HandlesPersonaCrudReadActions;
    use HandlesPersonaCrudWriteActions;
    use HandlesPersonaDatatableActions;
    use HandlesPersonaEstadoActions;
    use HandlesPersonaPermissionConstants;
    use HandlesPersonaUserRoleActions;

    protected PersonaService $personaService;

    protected UbicacionService $ubicacionService;

    protected TemaRepository $temaRepo;

    public function __construct(
        PersonaService $personaService,
        UbicacionService $ubicacionService,
        TemaRepository $temaRepo
    ) {
        $this->initializePersonaController($personaService, $ubicacionService, $temaRepo);
    }
}
