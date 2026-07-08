<?php

namespace App\Livewire\GuiasAprendizaje;

use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeGestionarResultadosAssignActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeGestionarResultadosConfirmActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeGestionarResultadosDesassignActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeGestionarResultadosLoadHelpers;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeGestionarResultadosMiscActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeGestionarResultadosModalActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeGestionarResultadosMountHelpers;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeGestionarResultadosRenderActions;
use Livewire\Component;

class GestionarResultados extends Component
{
    use HandlesGuiaAprendizajeGestionarResultadosAssignActions;
    use HandlesGuiaAprendizajeGestionarResultadosConfirmActions;
    use HandlesGuiaAprendizajeGestionarResultadosDesassignActions;
    use HandlesGuiaAprendizajeGestionarResultadosLoadHelpers;
    use HandlesGuiaAprendizajeGestionarResultadosMiscActions;
    use HandlesGuiaAprendizajeGestionarResultadosModalActions;
    use HandlesGuiaAprendizajeGestionarResultadosMountHelpers;
    use HandlesGuiaAprendizajeGestionarResultadosRenderActions;

    public $guia = null;

    public $resultadosAsignados;

    public $resultadosDisponibles;

    public $showAsignarModal = false;

    public $resultadoSeleccionado = null;

    public $showConfirmarCierre = false;

    protected $listeners = [
        'openGestionarResultadosModal' => 'abrirModal',
        'confirmAction' => 'handleConfirmedAction',
    ];
}
