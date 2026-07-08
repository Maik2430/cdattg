<?php

namespace App\Livewire\Instructores;

use App\Livewire\Instructores\Concerns\HandlesInstructorIndexComputedProperties;
use App\Livewire\Instructores\Concerns\HandlesInstructorIndexCrudActions;
use App\Livewire\Instructores\Concerns\HandlesInstructorIndexEspecialidadesHelpers;
use App\Livewire\Instructores\Concerns\HandlesInstructorIndexFilterActions;
use App\Livewire\Instructores\Concerns\HandlesInstructorIndexModalCloseActions;
use App\Livewire\Instructores\Concerns\HandlesInstructorIndexModalOpenActions;
use App\Livewire\Instructores\Concerns\HandlesInstructorIndexModalOpenEspecialidadFichaActions;
use App\Livewire\Instructores\Concerns\HandlesInstructorIndexMountHelpers;
use App\Livewire\Instructores\Concerns\HandlesInstructorIndexNotificationActions;
use App\Livewire\Instructores\Concerns\HandlesInstructorIndexRenderActions;
use Livewire\Component;
use Livewire\WithPagination;

class InstructorIndex extends Component
{
    use HandlesInstructorIndexComputedProperties;
    use HandlesInstructorIndexCrudActions;
    use HandlesInstructorIndexEspecialidadesHelpers;
    use HandlesInstructorIndexFilterActions;
    use HandlesInstructorIndexModalCloseActions;
    use HandlesInstructorIndexModalOpenActions;
    use HandlesInstructorIndexModalOpenEspecialidadFichaActions;
    use HandlesInstructorIndexMountHelpers;
    use HandlesInstructorIndexNotificationActions;
    use HandlesInstructorIndexRenderActions;
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $statusFilter = '';

    public $especialidadFilter = '';

    public $regionalFilter = '';

    public $showCreateModal = false;

    public $showEditModal = false;

    public $showShowModal = false;

    public $showDeleteModal = false;

    public $showEspecialidadesModal = false;

    public $showFichasModal = false;

    public $selectedInstructor = null;

    public $fichasAsignadas = null;

    public $especialidadesAsignadas = null;

    public $redesConocimientoDisponibles = null;

    public $selectedId = null;

    public $regionales = [];

    public $especialidades = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'statusFilter' => ['except' => ''],
        'especialidadFilter' => ['except' => ''],
        'regionalFilter' => ['except' => ''],
    ];

    protected $listeners = [
        'instructorCreado' => '$refresh',
        'instructorActualizado' => '$refresh',
        'instructorEliminado' => '$refresh',
        'closeModal' => 'handleCloseModal',
        'notify' => 'showNotification',
        'refreshPagination' => '$refresh',
    ];

    public function mount()
    {
        $this->cargarDatosFiltros();
    }
}
