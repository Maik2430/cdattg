<?php

namespace App\Livewire\Competencias;

use App\Livewire\Competencias\Concerns\HandlesGestionarResultadosAssignActions;
use App\Livewire\Competencias\Concerns\HandlesGestionarResultadosFilterActions;
use App\Livewire\Competencias\Concerns\HandlesGestionarResultadosModalActions;
use App\Livewire\Competencias\Concerns\HandlesGestionarResultadosMountHelpers;
use App\Livewire\Competencias\Concerns\HandlesGestionarResultadosRenderActions;
use Livewire\Component;
use Livewire\WithPagination;

class GestionarResultados extends Component
{
    use HandlesGestionarResultadosAssignActions;
    use HandlesGestionarResultadosFilterActions;
    use HandlesGestionarResultadosModalActions;
    use HandlesGestionarResultadosMountHelpers;
    use HandlesGestionarResultadosRenderActions;
    use WithPagination;

    public $competencia;

    public $searchAsignados = '';

    public $searchDisponibles = '';

    public $perPage = 10;

    public $selectedResultados = [];

    public $showAsociarModal = false;

    public $showDesasociarModal = false;

    public $selectedResultado = null;

    protected $queryString = [
        'searchAsignados' => ['except' => ''],
        'searchDisponibles' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected $listeners = [
        'resultadoAsociado' => '$refresh',
        'resultadoDesasociado' => '$refresh',
        'closeModal' => 'handleCloseModal',
        'notify' => 'showNotification',
    ];
}
