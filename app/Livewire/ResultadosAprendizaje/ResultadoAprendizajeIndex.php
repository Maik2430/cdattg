<?php

namespace App\Livewire\ResultadosAprendizaje;

use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeIndexCompetenciasAssignActions;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeIndexCompetenciasDesassignActions;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeIndexCompetenciasLoadHelpers;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeIndexCompetenciasModalActions;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeIndexCompetenciasSelectionActions;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeIndexCrudActions;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeIndexFilterActions;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeIndexModalActions;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeIndexMountHelpers;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeIndexNotificationActions;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeIndexRenderActions;
use Livewire\Component;
use Livewire\WithPagination;

class ResultadoAprendizajeIndex extends Component
{
    use HandlesResultadoAprendizajeIndexCompetenciasAssignActions;
    use HandlesResultadoAprendizajeIndexCompetenciasDesassignActions;
    use HandlesResultadoAprendizajeIndexCompetenciasLoadHelpers;
    use HandlesResultadoAprendizajeIndexCompetenciasModalActions;
    use HandlesResultadoAprendizajeIndexCompetenciasSelectionActions;
    use HandlesResultadoAprendizajeIndexCrudActions;
    use HandlesResultadoAprendizajeIndexFilterActions;
    use HandlesResultadoAprendizajeIndexModalActions;
    use HandlesResultadoAprendizajeIndexMountHelpers;
    use HandlesResultadoAprendizajeIndexNotificationActions;
    use HandlesResultadoAprendizajeIndexRenderActions;
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $statusFilter = '';

    public $competenciaFilter = '';

    public $showCreateModal = false;

    public $showEditModal = false;

    public $showShowModal = false;

    public $showDeleteModal = false;

    public $showCompetenciasModal = false;

    public $selectedResultado = null;

    public $selectedId = null;

    public $searchCompetencias = '';

    public $competenciasSeleccionadas = [];

    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'statusFilter' => ['except' => ''],
        'competenciaFilter' => ['except' => ''],
    ];

    protected $listeners = [
        'resultadoCreado' => '$refresh',
        'resultadoActualizado' => '$refresh',
        'resultadoEliminado' => '$refresh',
        'closeModal' => 'handleCloseModal',
        'notify' => 'showNotification',
        'refreshModal' => 'handleRefreshModal',
        'refreshPagination' => '$refresh',
        'competenciasActualizadas' => '$refresh',
    ];
}
