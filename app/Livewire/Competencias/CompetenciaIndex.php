<?php

namespace App\Livewire\Competencias;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Competencia;
use Livewire\Attributes\On;

class CompetenciaIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;  // Valor por defecto más razonable
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $statusFilter = '';
    public $vigenciaFilter = '';
    
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showShowModal = false;
    public $showDeleteModal = false;
    public $selectedCompetencia = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],  // Cambiar el except también
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

    public function mount()
    {
        // No establecer perPage aquí, ya que se establece en el queryString
        // $this->perPage = 15;  // ← Esto causa el problema
        \Log::info('CompetenciaIndex mounted');
    }

    public function render()
    {
        $query = Competencia::with(['programasFormacion']);
        
        // Debug logging
        \Log::info('CompetenciaIndex render - Filtros:', [
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'vigenciaFilter' => $this->vigenciaFilter,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'perPage' => $this->perPage,
            'page' => $this->page ?? 1
        ]);
        
        // Debug del query SQL
        \Log::info('Query SQL before paginate: ' . $query->toSql());
        \Log::info('Query bindings: ' . json_encode($query->getBindings()));
        
        // Filtro de búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('codigo', 'like', '%' . $this->search . '%')
                  ->orWhere('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            });
        }
        
        // Filtro de estado
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter === '1');
        }
        
        // Filtro de vigencia
        if ($this->vigenciaFilter !== '') {
            if ($this->vigenciaFilter === 'vigentes') {
                $query->where('fecha_inicio', '<=', now())
                      ->where('fecha_fin', '>=', now());
            } elseif ($this->vigenciaFilter === 'no_vigentes') {
                $query->where(function ($q) {
                    $q->where('fecha_inicio', '>', now())
                      ->orWhere('fecha_fin', '<', now())
                      ->orWhereNull('fecha_inicio')
                      ->orWhereNull('fecha_fin');
                });
            }
        }
        
        // Ordenamiento
        $query->orderBy($this->sortField, $this->sortDirection);
        
        $competencias = $query->paginate($this->perPage);
        
        // Debug logging
        \Log::info('CompetenciaIndex render - Resultados:', [
            'total' => $competencias->total(),
            'count' => $competencias->count(),
            'currentPage' => $competencias->currentPage(),
            'lastPage' => $competencias->lastPage(),
            'perPage' => $competencias->perPage(),
            'hasMorePages' => $competencias->hasMorePages()
        ]);
        
        // Debug específico para la página 2
        if ($competencias->currentPage() == 2) {
            \Log::info('Página 2 específica - Items encontrados: ' . $competencias->count());
            $itemsIds = collect($competencias->items())->pluck('id')->toArray();
            \Log::info('Página 2 específica - Items IDs: ' . json_encode($itemsIds));
            \Log::info('Página 2 específica - LastPage: ' . $competencias->lastPage());
            \Log::info('Página 2 específica - Total: ' . $competencias->total());
            \Log::info('Página 2 específica - PerPage: ' . $competencias->perPage());
        }
        
        // Debug general de paginación
        \Log::info('Paginación Debug:', [
            'currentPage' => $competencias->currentPage(),
            'lastPage' => $competencias->lastPage(),
            'total' => $competencias->total(),
            'perPage' => $competencias->perPage(),
            'hasMorePages' => $competencias->hasMorePages(),
            'count' => $competencias->count(),
            'firstItem' => $competencias->firstItem(),
            'lastItem' => $competencias->lastItem()
        ]);

        return view('livewire.competencias.competencia-index', compact('competencias'));
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function openEditModal($competenciaId)
    {
        $this->selectedCompetencia = Competencia::find($competenciaId);
        $this->showEditModal = true;
        
        if ($this->showShowModal) {
            $this->showShowModal = false;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedCompetencia = null;
    }

    public function openShowModal($competenciaId)
    {
        $this->selectedCompetencia = Competencia::with(['programasFormacion', 'resultadosCompetencia'])->find($competenciaId);
        $this->showShowModal = true;
    }

    public function handleCloseModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showShowModal = false;
        $this->showDeleteModal = false;
        $this->selectedCompetencia = null;
    }

    public function showNotification($data)
    {
        // Este método maneja las notificaciones desde el backend
        // El JavaScript se encargará de mostrarlas visualmente
    }

    public function handleRefreshModal()
    {
        // Este método se llama cuando se necesita refrescar el modal
        // Forzamos la recarga de los datos del selectedCompetencia
        if ($this->selectedCompetencia) {
            $this->selectedCompetencia->refresh();
        }
    }

    public function closeShowModal()
    {
        $this->showShowModal = false;
        $this->selectedCompetencia = null;
    }

    public function confirmDelete($competenciaId)
    {
        $this->selectedCompetencia = Competencia::with(['programasFormacion'])->find($competenciaId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedCompetencia = null;
    }

    public function deleteCompetencia($competenciaId)
    {
        $competencia = Competencia::with(['programasFormacion'])->find($competenciaId);
        
        if (!$competencia) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Competencia no encontrada',
            ]);
            return;
        }
        
        // Verificar si tiene programas asociados
        if ($competencia->programasFormacion->count() > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No se puede eliminar la competencia. Tiene ' . $competencia->programasFormacion->count() . ' programas asociados.',
            ]);
            return;
        }
        
        try {
            $competencia->delete();
            $this->closeDeleteModal();
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Competencia eliminada correctamente',
            ]);
            $this->dispatch('competenciaEliminada');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar la competencia: ' . $e->getMessage(),
            ]);
        }
    }

    public function toggleStatus($competenciaId)
    {
        $competencia = Competencia::find($competenciaId);
        
        if ($competencia) {
            // Cambiar estado usando booleano
            $competencia->status = !$competencia->status;
            $competencia->user_edit_id = auth()->id();
            $competencia->save();

            $statusText = $competencia->status ? 'activada' : 'desactivada';
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Competencia {$statusText} correctamente"
            ]);

            // Si estamos en el modal de detalles, actualizar los datos pero mantener el modal abierto
            if ($this->showShowModal && $this->selectedCompetencia && $this->selectedCompetencia->id == $competenciaId) {
                // Recargar los datos actualizados del modelo
                $this->selectedCompetencia->refresh();
                
                // Forzar re-render del componente para actualizar la UI
                $this->dispatch('refreshModal');
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
        \Log::info('Search updated: ' . $this->search);
    }
    
    public function updatedStatusFilter()
    {
        $this->resetPage();
        \Log::info('Status filter updated: ' . $this->statusFilter);
    }
    
    public function updatedVigenciaFilter()
    {
        $this->resetPage();
        \Log::info('Vigencia filter updated: ' . $this->vigenciaFilter);
    }
    
    public function updatedPerPage()
    {
        $this->resetPage();
        \Log::info('PerPage updated: ' . $this->perPage);
        \Log::info('After resetPage - Current page: ' . $this->page);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->vigenciaFilter = '';
        $this->resetPage();
        \Log::info('Filters cleared');
    }
    
    // Método específico para paginación (no resetear página)
    public function gotoPage($page)
    {
        \Log::info('gotoPage called with page: ' . $page);
        \Log::info('Before gotoPage - Current page: ' . $this->page);
        
        $this->page = $page;
        
        \Log::info('After gotoPage - New page: ' . $this->page);
        \Log::info('Going to page: ' . $page);
    }
    
    // Sobrescribir el método page para debugging
    public function setPage($page)
    {
        \Log::info('setPage called with: ' . $page);
        $this->page = $page;
    }
    
    // Método para forzar refresh de paginación
    public function refreshPagination()
    {
        \Log::info('Refreshing pagination');
        $this->dispatch('refreshPagination');
    }
    
    // Método para debugging de paginación
    public function debugPagination()
    {
        \Log::info('=== DEBUG PAGINATION ===');
        \Log::info('Current page: ' . $this->page);
        \Log::info('Per page: ' . $this->perPage);
        
        // Forzar render para ver qué pasa
        $this->dispatch('debugPagination');
    }
}
