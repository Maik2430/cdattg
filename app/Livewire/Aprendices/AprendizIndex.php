<?php

namespace App\Livewire\Aprendices;

use App\Livewire\Aprendices\Concerns\HandlesAprendizIndexCrudActions;
use App\Livewire\Aprendices\Concerns\HandlesAprendizIndexFilterActions;
use App\Livewire\Aprendices\Concerns\HandlesAprendizIndexModalActions;
use App\Livewire\Aprendices\Concerns\HandlesAprendizIndexMountHelpers;
use App\Livewire\Aprendices\Concerns\HandlesAprendizIndexNotificationActions;
use App\Livewire\Aprendices\Concerns\HandlesAprendizIndexRenderActions;
use App\Livewire\Concerns\MapsLivewireRefreshListeners;
use Livewire\Component;
use Livewire\WithPagination;

class AprendizIndex extends Component
{
    use HandlesAprendizIndexCrudActions;
    use HandlesAprendizIndexFilterActions;
    use HandlesAprendizIndexModalActions;
    use HandlesAprendizIndexMountHelpers;
    use HandlesAprendizIndexNotificationActions;
    use HandlesAprendizIndexRenderActions;
    use MapsLivewireRefreshListeners;
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $page = 1;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $fichaFilter = '';

    public $programaFilter = '';

    public $regionalFilter = '';

    public $statusFilter = '';

    public $showCreateModal = false;

    public $showEditModal = false;

    public $showShowModal = false;

    public $showDeleteModal = false;

    public $selectedAprendiz = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'page' => ['except' => 1],
    ];

    protected function getListeners(): array
    {
        return array_merge(
            $this->refreshListeners(['aprendizCreado', 'aprendizActualizado', 'aprendizEliminado']),
            [
                'closeModal' => 'handleCloseModal',
                'notify' => 'showNotification',
            ]
        );
    }
}
