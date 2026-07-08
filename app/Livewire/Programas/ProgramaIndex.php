<?php

namespace App\Livewire\Programas;

use App\Livewire\Programas\Concerns\HandlesProgramaIndexCrudActions;
use App\Livewire\Programas\Concerns\HandlesProgramaIndexFilterActions;
use App\Livewire\Programas\Concerns\HandlesProgramaIndexModalActions;
use App\Livewire\Programas\Concerns\HandlesProgramaIndexMountHelpers;
use App\Livewire\Programas\Concerns\HandlesProgramaIndexNotificationActions;
use App\Livewire\Programas\Concerns\HandlesProgramaIndexRenderActions;
use Livewire\Component;
use Livewire\WithPagination;

class ProgramaIndex extends Component
{
    use HandlesProgramaIndexCrudActions;
    use HandlesProgramaIndexFilterActions;
    use HandlesProgramaIndexModalActions;
    use HandlesProgramaIndexMountHelpers;
    use HandlesProgramaIndexNotificationActions;
    use HandlesProgramaIndexRenderActions;
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $redConocimientoFilter = '';

    public $nivelFilter = '';

    public $statusFilter = '';

    public $showCreateModal = false;

    public $showEditModal = false;

    public $showShowModal = false;

    public $showDeleteModal = false;

    public $selectedPrograma = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected $listeners = [
        'programaCreado' => '$refresh',
        'programaActualizado' => '$refresh',
        'programaEliminado' => '$refresh',
        'closeModal' => 'handleCloseModal',
        'notify' => 'showNotification',
    ];
}
