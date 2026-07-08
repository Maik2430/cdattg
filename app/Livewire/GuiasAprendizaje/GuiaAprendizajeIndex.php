<?php

namespace App\Livewire\GuiasAprendizaje;

use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeIndexCrudActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeIndexFilterActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeIndexModalActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeIndexMountHelpers;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeIndexNotificationActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeIndexRenderActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeIndexResultadosAssignActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeIndexResultadosDesassignActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeIndexResultadosLoadHelpers;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeIndexResultadosModalActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeIndexResultadosSelectionActions;
use Livewire\Component;
use Livewire\WithPagination;

class GuiaAprendizajeIndex extends Component
{
    use HandlesGuiaAprendizajeIndexCrudActions;
    use HandlesGuiaAprendizajeIndexFilterActions;
    use HandlesGuiaAprendizajeIndexModalActions;
    use HandlesGuiaAprendizajeIndexMountHelpers;
    use HandlesGuiaAprendizajeIndexNotificationActions;
    use HandlesGuiaAprendizajeIndexRenderActions;
    use HandlesGuiaAprendizajeIndexResultadosAssignActions;
    use HandlesGuiaAprendizajeIndexResultadosDesassignActions;
    use HandlesGuiaAprendizajeIndexResultadosLoadHelpers;
    use HandlesGuiaAprendizajeIndexResultadosModalActions;
    use HandlesGuiaAprendizajeIndexResultadosSelectionActions;
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $statusFilter = '';

    public $resultadoFilter = '';

    public $showCreateModal = false;

    public $showEditModal = false;

    public $showShowModal = false;

    public $showDeleteModal = false;

    public $showGestionarResultadosModal = false;

    public $selectedGuia = null;

    public $selectedId = null;

    public $searchResultados = '';

    public $resultadosSeleccionados = [];

    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'statusFilter' => ['except' => ''],
        'resultadoFilter' => ['except' => ''],
    ];

    protected $listeners = [
        'guiaCreada' => '$refresh',
        'guiaActualizada' => '$refresh',
        'guiaEliminada' => '$refresh',
        'closeModal' => 'handleCloseModal',
        'notify' => 'showNotification',
        'refreshModal' => 'handleRefreshModal',
        'refreshPagination' => '$refresh',
        'confirmAction' => 'handleConfirmedAction',
    ];
}
