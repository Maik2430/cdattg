<?php

namespace App\Livewire\Fichas;

use App\Livewire\Fichas\Concerns\HandlesFichaIndexAprendicesAssignActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexAprendicesDesassignActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexAprendicesLoadHelpers;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexAprendicesModalActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexAprendicesSelectionActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexCrudActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexFilterActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexInstructoresAssignActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexInstructoresDesassignActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexInstructoresLoadHelpers;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexInstructoresModalActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexInstructoresSelectionActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexModalActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexMountHelpers;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexNotificationActions;
use App\Livewire\Fichas\Concerns\HandlesFichaIndexRenderActions;
use Livewire\Component;
use Livewire\WithPagination;

class FichaIndex extends Component
{
    use HandlesFichaIndexAprendicesAssignActions;
    use HandlesFichaIndexAprendicesDesassignActions;
    use HandlesFichaIndexAprendicesLoadHelpers;
    use HandlesFichaIndexAprendicesModalActions;
    use HandlesFichaIndexAprendicesSelectionActions;
    use HandlesFichaIndexCrudActions;
    use HandlesFichaIndexFilterActions;
    use HandlesFichaIndexInstructoresAssignActions;
    use HandlesFichaIndexInstructoresDesassignActions;
    use HandlesFichaIndexInstructoresLoadHelpers;
    use HandlesFichaIndexInstructoresModalActions;
    use HandlesFichaIndexInstructoresSelectionActions;
    use HandlesFichaIndexModalActions;
    use HandlesFichaIndexMountHelpers;
    use HandlesFichaIndexNotificationActions;
    use HandlesFichaIndexRenderActions;
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $page = 1;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    // Filtros adicionales
    public $programaFilter = '';

    public $regionalFilter = '';

    public $sedeFilter = '';

    public $statusFilter = '';

    // Modales
    public $showCreateModal = false;

    public $showEditModal = false;

    public $showShowModal = false;

    public $showDeleteModal = false;

    public $showGestionarAprendicesModal = false;

    public $selectedFicha = null;

    public $selectedPersonas = [];

    public $selectAllPersonas = false;

    public $searchPersona = '';

    public $personasDisponibles = [];

    // Propiedades para desasignación
    public $selectedAprendicesAsignados = [];

    public $selectAllAprendicesAsignados = false;

    // Propiedades para gestión de instructores
    public $showGestionarInstructoresModal = false;

    public $selectedFichaInstructores = null;

    public $instructoresDisponibles = [];

    public $selectedInstructores = [];

    public $selectAllInstructores = false;

    public $searchInstructor = '';

    public $instructoresAsignados = [];

    public $selectedInstructoresAsignados = [];

    public $selectAllInstructoresAsignados = false;

    // Datos para filtros
    public $programas;

    public $regionales;

    public $sedes;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'page' => ['except' => 1],
    ];

    protected $listeners = [
        'fichaCreada' => '$refresh',
        'fichaActualizada' => '$refresh',
        'fichaEliminada' => '$refresh',
        'closeModal' => 'handleCloseModal',
        'notify' => 'showNotification',
    ];

    public function mount()
    {
        $this->loadFiltersData();
    }
}
