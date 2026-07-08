<?php

namespace App\Livewire\ControlSeguimiento;

use App\Livewire\ControlSeguimiento\Concerns\HandlesIngresoSalidaDashboardDataActions;
use App\Livewire\ControlSeguimiento\Concerns\HandlesIngresoSalidaDashboardFechaActions;
use App\Livewire\ControlSeguimiento\Concerns\HandlesIngresoSalidaDashboardMountHelpers;
use App\Livewire\ControlSeguimiento\Concerns\HandlesIngresoSalidaDashboardRenderActions;
use App\Repositories\SedeRepository;
use App\Services\PersonaIngresoSalidaService;
use Livewire\Component;

class IngresoSalidaDashboard extends Component
{
    use HandlesIngresoSalidaDashboardDataActions;
    use HandlesIngresoSalidaDashboardFechaActions;
    use HandlesIngresoSalidaDashboardMountHelpers;
    use HandlesIngresoSalidaDashboardRenderActions;

    public $sedes = [];

    public $estadisticasPorSede = [];

    public $estadisticasGenerales = [];

    public $estadisticasPorHora = [];

    public $frecuenciaActualizacion = '5mins';

    public $tiposPersona = [];

    public $configuracionTiposPersona = [];

    public $fechaSeleccionada;

    public $tieneFechaAnterior = false;

    public $tieneFechaSiguiente = false;

    public $eventosRecientes = [];

    protected SedeRepository $sedeRepository;

    protected PersonaIngresoSalidaService $personaIngresoSalidaService;
}
