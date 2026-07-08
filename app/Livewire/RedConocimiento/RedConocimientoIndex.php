<?php

namespace App\Livewire\RedConocimiento;

use App\Livewire\RedConocimiento\Concerns\HandlesRedConocimientoIndexCrudActions;
use App\Livewire\RedConocimiento\Concerns\HandlesRedConocimientoIndexFilterActions;
use App\Livewire\RedConocimiento\Concerns\HandlesRedConocimientoIndexModalActions;
use App\Livewire\RedConocimiento\Concerns\HandlesRedConocimientoIndexMountHelpers;
use App\Livewire\RedConocimiento\Concerns\HandlesRedConocimientoIndexNotificationActions;
use App\Livewire\RedConocimiento\Concerns\HandlesRedConocimientoIndexRenderActions;
use Livewire\Component;
use Livewire\WithPagination;

class RedConocimientoIndex extends Component
{
    use HandlesRedConocimientoIndexCrudActions;
    use HandlesRedConocimientoIndexFilterActions;
    use HandlesRedConocimientoIndexModalActions;
    use HandlesRedConocimientoIndexMountHelpers;
    use HandlesRedConocimientoIndexNotificationActions;
    use HandlesRedConocimientoIndexRenderActions;
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $statusFilter = '';

    public $regionalFilter = '';

    public $showCreateModal = false;

    public $showEditModal = false;

    public $showShowModal = false;

    public $showDeleteModal = false;

    public $selectedRed = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'statusFilter' => ['except' => ''],
        'regionalFilter' => ['except' => ''],
    ];

    protected $listeners = [
        'redCreada' => '$refresh',
        'redActualizada' => '$refresh',
        'redEliminada' => '$refresh',
        'closeModal' => 'handleCloseModal',
        'notify' => 'showNotification',
        'refreshModal' => 'handleRefreshModal',
    ];
}
