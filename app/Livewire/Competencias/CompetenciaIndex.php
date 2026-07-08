<?php

namespace App\Livewire\Competencias;

use App\Livewire\Competencias\Concerns\HandlesCompetenciaIndexCrudActions;
use App\Livewire\Competencias\Concerns\HandlesCompetenciaIndexFilterActions;
use App\Livewire\Competencias\Concerns\HandlesCompetenciaIndexModalActions;
use App\Livewire\Competencias\Concerns\HandlesCompetenciaIndexMountHelpers;
use App\Livewire\Competencias\Concerns\HandlesCompetenciaIndexNotificationActions;
use App\Livewire\Competencias\Concerns\HandlesCompetenciaIndexRenderActions;
use App\Livewire\Competencias\Concerns\HandlesCompetenciaIndexResultadosAssignActions;
use App\Livewire\Competencias\Concerns\HandlesCompetenciaIndexResultadosDesassignActions;
use App\Livewire\Competencias\Concerns\HandlesCompetenciaIndexResultadosLoadHelpers;
use App\Livewire\Competencias\Concerns\HandlesCompetenciaIndexResultadosModalActions;
use App\Livewire\Competencias\Concerns\HandlesCompetenciaIndexResultadosSelectionActions;
use Livewire\Component;
use Livewire\WithPagination;

class CompetenciaIndex extends Component
{
    use HandlesCompetenciaIndexCrudActions;
    use HandlesCompetenciaIndexFilterActions;
    use HandlesCompetenciaIndexModalActions;
    use HandlesCompetenciaIndexMountHelpers;
    use HandlesCompetenciaIndexNotificationActions;
    use HandlesCompetenciaIndexRenderActions;
    use HandlesCompetenciaIndexResultadosAssignActions;
    use HandlesCompetenciaIndexResultadosDesassignActions;
    use HandlesCompetenciaIndexResultadosLoadHelpers;
    use HandlesCompetenciaIndexResultadosModalActions;
    use HandlesCompetenciaIndexResultadosSelectionActions;
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $statusFilter = '';

    public $vigenciaFilter = '';

    public $showCreateModal = false;

    public $showEditModal = false;

    public $showShowModal = false;

    public $showDeleteModal = false;

    public $showResultadosModal = false;

    public $searchResultados = '';

    public $selectAll = false;

    public $resultadosSeleccionados = [];

    public $selectedCompetencia = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'statusFilter' => ['except' => ''],
        'vigenciaFilter' => ['except' => ''],
    ];

    protected $listeners = [
        'competenciaCreada' => '$refresh',
        'competenciaActualizada' => '$refresh',
        'competenciaEliminada' => '$refresh',
        'closeModal' => 'handleCloseModal',
        'notify' => 'showNotification',
        'refreshModal' => 'handleRefreshModal',
        'refreshPagination' => '$refresh',
    ];
}
