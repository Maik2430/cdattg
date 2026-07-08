<?php

namespace App\Livewire;

use App\Livewire\Concerns\IngresoSalida\HandlesIngresoSalidaModalActions;
use App\Livewire\Concerns\IngresoSalida\HandlesIngresoSalidaMountHelpers;
use App\Livewire\Concerns\IngresoSalida\HandlesIngresoSalidaRegistroActions;
use App\Livewire\Concerns\IngresoSalida\HandlesIngresoSalidaRenderActions;
use App\Livewire\Concerns\IngresoSalida\HandlesIngresoSalidaSearchActions;
use Livewire\Component;

class IngresoSalidaComponent extends Component
{
    use HandlesIngresoSalidaModalActions;
    use HandlesIngresoSalidaMountHelpers;
    use HandlesIngresoSalidaRegistroActions;
    use HandlesIngresoSalidaRenderActions;
    use HandlesIngresoSalidaSearchActions;

    // Búsqueda
    public $numeroDocumento = '';

    public $personaEncontrada = null;

    public $mostrarFormulario = false;

    public $modoEdicion = false;

    // Registro de ingreso/salida
    public $sedeId = null;

    public $sedeSeleccionada = null;

    public $mostrarModalSede = false;

    public $accionPendiente = null;

    public $observaciones = '';

    // Estado de carga
    public $procesando = false;

    public $mensaje = '';

    public $tipoMensaje = '';

    // Datos para el formulario de persona
    public $personaId = null;

    public $datosPersona = [];

    protected $listeners = ['limpiarBusqueda'];
}
