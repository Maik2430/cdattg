# Guía de Componentes Livewire - SENA CDATTG

## Objetivo

Documentación para que otros desarrolladores puedan reutilizar los componentes Livewire ya creados en nuevos módulos del sistema.

## Componentes Disponibles

### 1. Sistema Global de Notificaciones
**Ubicación**: `resources/js/global-notifications.js` + `resources/css/global-notifications.css`

#### Uso en CUALQUIER Componente Livewire

```php
// En cualquier método de componente Livewire
$this->dispatch('notify', [
    'type' => 'success',  // success, error, warning
    'message' => 'Operación completada correctamente'
]);
```

#### Tipos de Notificaciones
- **success**: Verde - Operaciones exitosas
- **error**: Rojo - Errores y fallos
- **warning**: Amarillo - Advertencias

#### Ejemplos de Uso
```php
// Creación
$this->dispatch('notify', [
    'type' => 'success',
    'message' => 'Usuario creado correctamente'
]);

// Error
$this->dispatch('notify', [
    'type' => 'error', 
    'message' => 'Error al guardar los datos'
]);

// Advertencia
$this->dispatch('notify', [
    'type' => 'warning',
    'message' => 'Revise los datos antes de continuar'
]);
```

---

### 2. Sistema de Gestión de Competencias
**Ubicación**: `app/Livewire/ResultadosAprendizaje/GestionarCompetencias.php`

#### Funcionalidades
- **Asociar competencias** a resultados de aprendizaje
- **Desasociar competencias** con confirmación
- **Redistribución automática** de duración
- **Estadísticas en tiempo real** de asignaciones
- **Interfaz moderna** con drag & drop visual

#### Uso en Componentes Index

```php
// En el componente principal (ej: ResultadoAprendizajeIndex.php)
public function openGestionCompetencias($resultadoId)
{
    $this->selectedId = $resultadoId;
    $this->showGestionCompetenciasModal = true;
}

// En la vista, agregar el botón
@can('GESTIONAR COMPETENCIAS')
    <button wire:click="openGestionCompetencias({{ $resultado->id }})" 
            class="btn-action btn-competencias" 
            title="Gestionar competencias">
        <i class="fas fa-link"></i>
    </button>
@endcan

// Modal en la vista
@if ($showGestionCompetenciasModal && $selectedId)
    <div class="modal-overlay" style="display: flex;">
        <div class="modal-container modal-lg">
            <livewire:resultados-aprendizaje.gestionar-competencias :resultado-id="$selectedId" />
        </div>
    </div>
@endif
```

#### Estructura del Componente

```php
<?php

namespace App\Livewire\TuModulo;

use Livewire\Component;
use App\Models\TuModelo;
use App\Models\OtroModelo;

class GestionarRelaciones extends Component
{
    public $modeloPrincipal;
    public $relacionesAsignadas;
    public $relacionesDisponibles;
    public $relacionSeleccionada;
    
    public $showAsignarModal = false;

    protected $listeners = [
        'relacionAsignada' => '$refresh',
        'relacionDesasociada' => '$refresh',
    ];

    public function mount($modeloId)
    {
        $this->modeloPrincipal = TuModelo::findOrFail($modeloId);
        $this->cargarRelaciones();
    }

    public function cargarRelaciones()
    {
        $this->relacionesAsignadas = $this->modeloPrincipal->relaciones()->get();
        
        $asignadasIds = $this->relacionesAsignadas->pluck('id')->toArray();
        $this->relacionesDisponibles = OtroModelo::whereNotIn('id', $asignadasIds)
            ->orderBy('nombre')
            ->get();
    }

    public function asignarRelacion()
    {
        $this->validate([
            'relacionSeleccionada' => 'required|exists:otra_tabla,id',
        ]);

        try {
            // Lógica de asignación
            $this->modeloPrincipal->relaciones()->attach($this->relacionSeleccionada, [
                'user_create_id' => auth()->id(),
                'user_edit_id' => auth()->id(),
            ]);

            $this->closeAsignarModal();
            $this->cargarRelaciones();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Relación asignada correctamente'
            ]);

            $this->dispatch('relacionAsignada');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al asignar: ' . $e->getMessage()
            ]);
        }
    }

    public function desasociarRelacion($relacionId)
    {
        try {
            $this->modeloPrincipal->relaciones()->detach($relacionId);
            $this->cargarRelaciones();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Relación desasociada correctamente'
            ]);

            $this->dispatch('relacionDesasociada');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al desasociar: ' . $e->getMessage()
            ]);
        }
    }
}
```

#### Vista de Gestión (Patrón)

```blade
<div>
    <!-- Header con información -->
    <div class="relation-header">
        <div class="relation-header-content">
            <div class="relation-header-info">
                <div class="relation-header-icon">
                    <i class="fas fa-link"></i>
                </div>
                <div class="relation-header-text">
                    <h2>{{ $modeloPrincipal->codigo }}</h2>
                    <p>{{ $modeloPrincipal->nombre }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-link"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $relacionesAsignadas->count() }}</h3>
                <p>Relaciones Asignadas</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $relacionesDisponibles->count() }}</h3>
                <p>Relaciones Disponibles</p>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="relations-container">
        <!-- Relaciones Asignadas -->
        <div class="relations-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-check-circle text-success"></i>
                    Relaciones Asignadas
                </h3>
                <span class="section-count">{{ $relacionesAsignadas->count() }}</span>
            </div>
            
            <div class="section-content">
                @if($relacionesAsignadas->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-unlink fa-3x text-gray-300 mb-3"></i>
                        <p>No hay relaciones asignadas</p>
                    </div>
                @else
                    <div class="relations-list">
                        @foreach($relacionesAsignadas as $relacion)
                            <div class="relation-card assigned">
                                <div class="relation-info">
                                    <div class="relation-badge">{{ $relacion->codigo }}</div>
                                    <div class="relation-name">{{ $relacion->nombre }}</div>
                                </div>
                                <div class="relation-actions">
                                    <button wire:click="desasociarRelacion({{ $relacion->id }})" 
                                            class="btn-action btn-danger">
                                        <i class="fas fa-unlink"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Relaciones Disponibles -->
        <div class="relations-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-plus-circle text-primary"></i>
                    Relaciones Disponibles
                </h3>
                <span class="section-count">{{ $relacionesDisponibles->count() }}</span>
            </div>
            
            <div class="section-content">
                @if($relacionesDisponibles->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-check-circle fa-3x text-gray-300 mb-3"></i>
                        <p>Todas las relaciones están asignadas</p>
                    </div>
                @else
                    <div class="relations-list">
                        @foreach($relacionesDisponibles as $relacion)
                            <div class="relation-card available">
                                <div class="relation-info">
                                    <div class="relation-badge">{{ $relacion->codigo }}</div>
                                    <div class="relation-name">{{ $relacion->nombre }}</div>
                                </div>
                                <div class="relation-actions">
                                    <button wire:click="asignarRelacion({{ $relacion->id }})" 
                                            class="btn-action btn-success">
                                        <i class="fas fa-link"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
```

#### CSS Específico

```css
/* Header */
.relation-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}

/* Estadísticas */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Contenedor de relaciones */
.relations-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

/* Secciones */
.relations-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.section-header {
    background: #f8fafc;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Lista de relaciones */
.relations-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.relation-card {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.relation-card.assigned {
    border-left: 4px solid #10b981;
}

.relation-card.available {
    border-left: 4px solid #3b82f6;
}
```

---

### 2. Componente Index Genérico (Pattern)
**Referencia**: `app/Livewire/ResultadosAprendizaje/ResultadoAprendizajeIndex.php`

#### Estructura Base para Nuevos Módulos

```php
<?php

namespace App\Livewire\TuModulo;

use Livewire\Component;
use App\Models\TuModelo;

class TuModeloIndex extends Component
{
    // Propiedades de filtrado
    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';
    public $otroFilter = '';
    
    // Propiedades de ordenamiento
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Propiedades de modales
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedId = null;

    protected $listeners = [
        'tuModeloCreado' => '$refresh',
        'tuModeloActualizado' => '$refresh',
        'tuModeloEliminado' => '$refresh',
    ];

    public function render()
    {
        $query = TuModelo::query();
        
        // Búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%');
            });
        }
        
        // Filtros
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }
        
        if ($this->otroFilter !== '') {
            $query->where('otro_campo', $this->otroFilter);
        }
        
        // Ordenamiento
        $query->orderBy($this->sortField, $this->sortDirection);
        
        // Paginación
        $modelos = $query->paginate($this->perPage);
        
        return view('livewire.tu-modulo.tu-modelo-index', compact('modelos'));
    }

    // Métodos de ordenamiento
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // Métodos de modales
    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function openEditModal($id)
    {
        $this->selectedId = $id;
        $this->showEditModal = true;
    }

    public function openShowModal($id)
    {
        $this->selectedId = $id;
        $this->showShowModal = true;
    }

    public function confirmDelete($id)
    {
        $this->selectedId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteTuModelo()
    {
        $modelo = TuModelo::find($this->selectedId);
        
        if (!$modelo) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Registro no encontrado'
            ]);
            return;
        }

        try {
            $modelo->delete();
            $this->closeDeleteModal();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Registro eliminado correctamente'
            ]);
            
            $this->dispatch('tuModeloEliminado');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ]);
        }
    }

    // Métodos de utilidad
    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'otroFilter']);
        $this->perPage = 10;
    }

    public function closeModal()
    {
        $this->reset(['showCreateModal', 'showEditModal', 'showDeleteModal', 'selectedId']);
    }

    public function closeDeleteModal()
    {
        $this->reset(['showDeleteModal', 'selectedId']);
    }
}
```

---

### 3. Componente Form Genérico (Pattern)
**Referencia**: `app/Livewire/ResultadosAprendizaje/ResultadoAprendizajeForm.php`

#### 🏗️ Estructura Base para Formularios

```php
<?php

namespace App\Livewire\TuModulo;

use Livewire\Component;
use App\Models\TuModelo;

class TuModeloForm extends Component
{
    // Propiedades del formulario
    public $nombre;
    public $codigo;
    public $descripcion;
    public $status = true;
    
    // Propiedades de control
    public $isEdit = false;
    public $modeloId;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'codigo' => 'required|string|max:20|unique:tu_tabla,codigo',
        'descripcion' => 'nullable|string|max:500',
        'status' => 'boolean',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio',
        'codigo.required' => 'El código es obligatorio',
        'codigo.unique' => 'Este código ya está registrado',
    ];

    public function mount($isEdit = false, $modeloId = null)
    {
        $this->isEdit = $isEdit;
        $this->modeloId = $modeloId;
        $this->status = true;

        if ($isEdit && $modeloId) {
            $this->loadModelo($modeloId);
        }
    }

    public function loadModelo($modeloId)
    {
        $modelo = TuModelo::find($modeloId);
        
        if (!$modelo) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Registro no encontrado'
            ]);
            return;
        }

        $this->nombre = $modelo->nombre;
        $this->codigo = $modelo->codigo;
        $this->descripcion = $modelo->descripcion;
        $this->status = $modelo->status;
    }

    public function save()
    {
        if ($this->isEdit) {
            $this->rules['codigo'] = 'required|string|max:20|unique:tu_tabla,codigo,' . $this->modeloId;
        }

        $this->validate();

        try {
            if ($this->isEdit) {
                // Actualizar
                $modelo = TuModelo::find($this->modeloId);
                
                if (!$modelo) {
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => 'Registro no encontrado'
                    ]);
                    return;
                }

                $modelo->update([
                    'nombre' => $this->nombre,
                    'codigo' => strtoupper($this->codigo),
                    'descripcion' => $this->descripcion,
                    'status' => $this->status,
                    'user_edit_id' => auth()->id(),
                ]);

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Registro actualizado correctamente'
                ]);
                
                $this->dispatch('tuModeloActualizado');
            } else {
                // Crear
                $modelo = TuModelo::create([
                    'nombre' => $this->nombre,
                    'codigo' => strtoupper($this->codigo),
                    'descripcion' => $this->descripcion,
                    'status' => $this->status,
                    'user_create_id' => auth()->id(),
                    'user_edit_id' => auth()->id(),
                ]);

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Registro creado correctamente'
                ]);
                
                $this->dispatch('tuModeloCreado');
            }

            $this->cancel();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al guardar: ' . $e->getMessage()
            ]);
        }
    }

    public function cancel()
    {
        $this->reset();
        $this->resetValidation();
        $this->mount();
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.tu-modulo.tu-modelo-form');
    }
}
```

---

### 4. Vista Index Genérica (Pattern)
**Referencia**: `resources/views/livewire/resultados-aprendizaje/resultado-aprendizaje-index.blade.php`

#### 🎨 Estructura Base para Vistas Index

```blade
<div>
    <!-- Barra de herramientas moderna -->
    <div class="toolbar">
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   class="search-input" 
                   placeholder="Buscar por nombre, código...">
        </div>
        
        <div class="toolbar-actions">
            <div class="filters">
                <select wire:model.live="statusFilter" class="filter-select">
                    <option value="">Todos los estados</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                </select>
                
                <select wire:model.live="perPage" class="results-select">
                    <option value="10">10 resultados</option>
                    <option value="25">25 resultados</option>
                    <option value="50">50 resultados</option>
                </select>
            </div>
            
            @can('CREAR TU_MODELO')
                <button wire:click="openCreateModal" class="btn-primary">
                    <i class="fas fa-plus"></i>
                    Nuevo Registro
                </button>
            @endcan
        </div>
    </div>

    <!-- Tabla moderna -->
    <div class="modern-table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th wire:click="sortBy('codigo')" class="sortable">
                        Código
                        @if ($sortField === 'codigo')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('nombre')" class="sortable">
                        Nombre
                        @if ($sortField === 'nombre')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($modelos as $modelo)
                    <tr>
                        <td>{{ $modelo->codigo }}</td>
                        <td>{{ $modelo->nombre }}</td>
                        <td>
                            <div wire:click="toggleStatus({{ $modelo->id }})" 
                                 class="badge badge-{{ $modelo->status ? 'success' : 'danger' }} badge-toggle cursor-pointer">
                                <i class="fas fa-circle"></i>
                                {{ $modelo->status ? 'Activo' : 'Inactivo' }}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                @can('VER TU_MODELO')
                                    <button wire:click="openShowModal({{ $modelo->id }})" 
                                            class="btn-action btn-view" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                @endcan
                                
                                @can('EDITAR TU_MODELO')
                                    <button wire:click="openEditModal({{ $modelo->id }})" 
                                            class="btn-action btn-edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                
                                @can('ELIMINAR TU_MODELO')
                                    <button wire:click="confirmDelete({{ $modelo->id }})" 
                                            class="btn-action btn-delete" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No hay registros encontrados</p>
                                <button wire:click="openCreateModal" class="btn-primary mt-2">
                                    <i class="fas fa-plus"></i>
                                    Crear primer registro
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación moderna -->
    <div class="pagination-container">
        {{ $modelos->links() }}
    </div>

    <!-- Modal Crear/Editar -->
    @if ($showCreateModal || $showEditModal)
        <div class="modal-overlay" style="display: flex;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $showCreateModal ? 'Crear' : 'Editar' }} Registro
                    </h5>
                    <button wire:click="closeModal" class="btn-close">&times;</button>
                </div>
                <div class="modal-body">
                    @livewire('tu-modulo.tu-modelo-form', [
                        'isEdit' => $showEditModal,
                        'modeloId' => $selectedId
                    ])
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Eliminar -->
    @if ($showDeleteModal)
        <div class="modal-overlay" style="display: flex;">
            <div class="modal-content modal-sm">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button wire:click="closeDeleteModal" class="btn-close">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <p>¿Está seguro de eliminar este registro?</p>
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button wire:click="closeDeleteModal" class="btn-secondary">
                        Cancelar
                    </button>
                    <button wire:click="deleteTuModelo" class="btn-danger">
                        <i class="fas fa-trash"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
```

---

### 5. Vista Form Genérica (Pattern)
**Referencia**: `resources/views/livewire/resultados-aprendizaje/resultado-aprendizaje-form.blade.php`

#### 🎨 Estructura Base para Vistas Form

```blade
<div class="modal-erp-container">
    <form wire:submit="save">
        <!-- Contenido principal -->
        <div class="modal-body-erp">
            <!-- Bloque 1 - Datos Principales -->
            <div class="section-block">
                <h6 class="section-title">Información Principal</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigo" class="form-label-erp">Código</label>
                            <input type="text" 
                                   id="codigo"
                                   wire:model="codigo" 
                                   class="form-control-erp @error('codigo') is-invalid @enderror" 
                                   placeholder="Ej: 001" 
                                   maxlength="20"
                                   required>
                            @error('codigo')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre" class="form-label-erp">Nombre</label>
                            <input type="text" 
                                   id="nombre"
                                   wire:model="nombre" 
                                   class="form-control-erp @error('nombre') is-invalid @enderror" 
                                   placeholder="Ej: Nombre del registro" 
                                   maxlength="255"
                                   required>
                            @error('nombre')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descripcion" class="form-label-erp">Descripción</label>
                            <textarea id="descripcion"
                                      wire:model="descripcion" 
                                      class="form-control-erp @error('descripcion') is-invalid @enderror" 
                                      placeholder="Descripción detallada..." 
                                      rows="3"
                                      maxlength="500"></textarea>
                            @error('descripcion')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bloque 2 - Estado -->
            <div class="section-block">
                <h6 class="section-title">Estado</h6>
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="status" wire:model="status">
                                <label class="form-check-label" for="status">
                                    <strong>Registro Activo</strong>
                                    <span class="form-text">Los registros activos pueden ser utilizados en el sistema</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer ERP -->
        <div class="modal-footer-erp">
            <div class="footer-actions">
                <button type="button" wire:click="cancel" class="btn-erp btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
                <button type="button" 
                        wire:click="save" 
                        class="btn-erp btn-primary">
                    <i class="fas fa-save"></i>
                    {{ $isEdit ? 'Actualizar' : 'Guardar' }} Registro
                </button>
            </div>
        </div>
    </form>
</div>
```

---

## 🚀 Pasos para Crear Nuevo Módulo

### 1. Crear Estructura de Archivos
```
app/Livewire/TuModulo/
├── TuModeloIndex.php
└── TuModeloForm.php

resources/views/livewire/tu-modulo/
├── tu-modelo-index.blade.php
└── tu-modelo-form.blade.php
```

### 2. Adaptar los Patrones
- Copiar y adaptar el código de los patrones anteriores
- Cambiar nombres de tablas, modelos y campos
- Ajustar validaciones según tus necesidades

### 3. Configurar Rutas
```php
// routes/web.php o routes/tu-modulo/web_tu_modulo.php
Route::resource('tu-recurso', TuModeloController::class);
```

### 4. Actualizar Permisos
```php
// database/seeders/PermissionSeeder.php
'CREAR TU_MODELO',
'VER TU_MODELO', 
'EDITAR TU_MODELO',
'ELIMINAR TU_MODELO'
```

### 5. Usar Sistema de Notificaciones
```php
// En cualquier método
$this->dispatch('notify', [
    'type' => 'success',
    'message' => 'Operación completada'
]);
```

---

## 🎨 Estilos CSS Disponibles

### CSS Global de Notificaciones
Ya está incluido en el layout principal, solo usa:
```php
$this->dispatch('notify', [...]);
```

### CSS de Competencias (Referencia)
`resources/css/competencias.css` - Contiene estilos modernos para:
- Toolbars
- Tables modernas
- Modales
- Badges
- Paginación

---

## 📋 Checklist de Implementación

- [ ] Crear estructura de archivos
- [ ] Adaptar componente Index
- [ ] Adaptar componente Form
- [ ] Crear vistas Blade
- [ ] Configurar rutas
- [ ] Actualizar permisos
- [ ] Probar notificaciones
- [ ] Probar CRUD completo
- [ ] Probar responsive design

---

## 🔗 Referencias Útiles

- **Componente ResultadosAprendizaje**: `app/Livewire/ResultadosAprendizaje/`
- **Vista Competencias**: `resources/views/competencias/index.blade.php`
- **CSS Global**: `resources/css/global-notifications.css`
- **Documentación**: `docs/global-notifications.md`

---

### 3. Sistema Global de Modales de Confirmación
**Ubicación**: `resources/js/global-modals.js` + `resources/css/global-modals.css`

#### Uso en CUALQUIER Componente Livewire

```php
// En cualquier método de componente Livewire
$this->dispatch('confirm', [
    'title' => 'Confirmar Eliminación',
    'message' => '¿Está seguro de eliminar este elemento?',
    'type' => 'danger',  // danger, warning, info
    'action' => 'eliminar', // nombre del método a ejecutar
    'params' => $id         // parámetros para el método
]);
```

#### Tipos de Modales
- **danger**: Rojo - Eliminación y acciones destructivas
- **warning**: Amarillo - Advertencias y cambios importantes
- **info**: Azul - Información y acciones neutrales

#### Ejemplos de Uso

```php
// Eliminación
$this->dispatch('confirm', [
    'title' => 'Confirmar Eliminación',
    'message' => '¿Está seguro de eliminar este resultado de aprendizaje?',
    'type' => 'danger',
    'action' => 'deleteResultado',
    'params' => $resultadoId
]);

// Cambio de estado
$this->dispatch('confirm', [
    'title' => 'Confirmar Cambio de Estado',
    'message' => '¿Desea cambiar el estado a inactivo?',
    'type' => 'warning',
    'action' => 'toggleStatus',
    'params' => $resultadoId
]);

// Asignación
$this->dispatch('confirm', [
    'title' => 'Confirmar Asignación',
    'message' => '¿Desea asignar esta competencia?',
    'type' => 'info',
    'action' => 'asignarCompetencia',
    'params' => ['competencia_id' => $competenciaId, 'resultado_id' => $resultadoId]
]);
```

#### Implementación del JavaScript Global

```javascript
// resources/js/global-modals.js
console.log('🚀 Global modals script loaded');

// Variables globales para el modal
let modalConfig = null;
let modalElement = null;

// Función para mostrar el modal
window.showGlobalModal = function(config) {
    modalConfig = config;
    modalElement = document.getElementById('globalConfirmModal');
    
    if (!modalElement) {
        console.error('Modal element not found');
        return;
    }
    
    // Configurar contenido
    const titleElement = modalElement.querySelector('.modal-title');
    const messageElement = modalElement.querySelector('.modal-message');
    const iconElement = modalElement.querySelector('.modal-icon');
    const confirmBtn = modalElement.querySelector('.btn-confirm');
    
    titleElement.textContent = config.title;
    messageElement.textContent = config.message;
    
    // Configurar icono según tipo
    iconElement.className = 'modal-icon fas fa-3x mb-3';
    switch(config.type) {
        case 'danger':
            iconElement.classList.add('text-danger');
            break;
        case 'warning':
            iconElement.classList.add('text-warning');
            break;
        case 'info':
            iconElement.classList.add('text-info');
            break;
        default:
            iconElement.classList.add('text-primary');
    }
    
    // Configurar botón de confirmación
    confirmBtn.className = 'btn btn-' + config.type;
    confirmBtn.textContent = getButtonText(config.type);
    
    // Mostrar modal
    modalElement.style.display = 'block';
    modalElement.classList.add('show');
};

// Función para cerrar el modal
window.closeGlobalModal = function() {
    if (modalElement) {
        modalElement.style.display = 'none';
        modalElement.classList.remove('show');
        modalConfig = null;
    }
};

// Función para confirmar acción
window.confirmGlobalModal = function() {
    if (!modalConfig) return;
    
    // Enviar evento a Livewire
    Livewire.dispatch('confirmAction', {
        action: modalConfig.action,
        params: modalConfig.params
    });
    
    // Cerrar modal
    closeGlobalModal();
};

// Función auxiliar para texto del botón
function getButtonText(type) {
    switch(type) {
        case 'danger':
            return 'Eliminar';
        case 'warning':
            return 'Confirmar';
        case 'info':
            return 'Aceptar';
        default:
            return 'Aceptar';
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeGlobalModal();
        }
    });
    
    // Cerrar modal al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (modalElement && e.target === modalElement) {
            closeGlobalModal();
        }
    });
});
```

#### Implementación del CSS Global

```css
/* resources/css/global-modals.css */
#globalConfirmModal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

#globalConfirmModal.show {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    max-width: 500px;
    width: 90%;
    overflow: hidden;
}

.modal-header {
    padding: 20px 24px 0;
    text-align: center;
}

.modal-title {
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.modal-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.modal-body {
    padding: 0 24px 20px;
    text-align: center;
}

.modal-message {
    font-size: 16px;
    color: #6b7280;
    margin: 0;
    line-height: 1.5;
}

.modal-footer {
    padding: 0 24px 24px;
    display: flex;
    gap: 12px;
    justify-content: center;
}

.modal-footer button {
    padding: 8px 24px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: #dc2626;
    color: white;
    border: 1px solid #dc2626;
}

.btn-danger:hover {
    background: #b91c1c;
}

.btn-warning {
    background: #f59e0b;
    color: white;
    border: 1px solid #f59e0b;
}

.btn-warning:hover {
    background: #d97706;
}

.btn-info {
    background: #3b82f6;
    color: white;
    border: 1px solid #3b82f6;
}

.btn-info:hover {
    background: #2563eb;
}

/* Animaciones */
.modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
```

#### HTML del Modal (Agregar al Layout Principal)

```html
<!-- En resources/views/vendor/adminlte/page.blade.php -->
<!-- Global Confirmation Modal -->
<div id="globalConfirmModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Confirmar Acción</h5>
        </div>
        <div class="modal-body">
            <div class="modal-icon fas fa-question-circle text-primary"></div>
            <p class="modal-message">¿Está seguro de realizar esta acción?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeGlobalModal()">
                Cancelar
            </button>
            <button type="button" class="btn btn-primary btn-confirm" onclick="confirmGlobalModal()">
                Confirmar
            </button>
        </div>
    </div>
</div>
```

#### Configuración de Vite

```javascript
// En vite.config.js
'global_modals_js': 'resources/js/global-modals.js',
'global_modals_css': 'resources/css/global-modals.css',
```

#### Integración en el Layout

```html
<!-- En resources/views/vendor/adminlte/page.blade.php -->
<!-- Global Modals JS -->
@vite(['resources/js/global-modals.js'])
<!-- Global Modals CSS -->
@vite(['resources/css/global-modals.css'])
```

#### Uso en Componentes Livewire

```php
<?php

namespace App\Livewire\TuModulo;

use Livewire\Component;

class TuComponente extends Component
{
    protected $listeners = [
        'confirmAction' => 'handleConfirmedAction',
    ];

    public function handleConfirmedAction($action, $params)
    {
        try {
            switch ($action) {
                case 'deleteResultado':
                    $this->deleteResultado($params);
                    break;
                case 'toggleStatus':
                    $this->toggleStatus($params);
                    break;
                case 'asignarCompetencia':
                    $this->asignarCompetencia($params);
                    break;
                // Agregar más casos según necesites
            }
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Acción completada correctamente'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al ejecutar la acción: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteResultado($id)
    {
        // Lógica de eliminación
        $resultado = Resultado::findOrFail($id);
        $resultado->delete();
    }

    public function toggleStatus($id)
    {
        // Lógica de cambio de estado
        $resultado = Resultado::findOrFail($id);
        $resultado->status = !$resultado->status;
        $resultado->save();
    }

    public function asignarCompetencia($params)
    {
        // Lógica de asignación
        $resultado = Resultado::findOrFail($params['resultado_id']);
        $competencia = Competencia::findOrFail($params['competencia_id']);
        $resultado->competencias()->attach($competencia->id);
    }
}
```

#### Ejemplos Prácticos

```php
// En un método de eliminación
public function confirmDelete($id)
{
    $this->dispatch('confirm', [
        'title' => 'Eliminar Resultado',
        'message' => '¿Está seguro de eliminar este resultado de aprendizaje? Esta acción no se puede deshacer.',
        'type' => 'danger',
        'action' => 'deleteResultado',
        'params' => $id
    ]);
}

// En un método de cambio de estado
public function confirmToggleStatus($id)
{
    $resultado = Resultado::findOrFail($id);
    $status = $resultado->status ? 'inactivo' : 'activo';
    
    $this->dispatch('confirm', [
        'title' => 'Cambiar Estado',
        'message' => "¿Desea cambiar el estado a {$status}?",
        'type' => 'warning',
        'action' => 'toggleStatus',
        'params' => $id
    ]);
}

// En un método de asignación
public function confirmAsignar($competenciaId, $resultadoId)
{
    $competencia = Competencia::findOrFail($competenciaId);
    
    $this->dispatch('confirm', [
        'title' => 'Asignar Competencia',
        'message' => "¿Desea asignar la competencia {$competencia->codigo}?",
        'type' => 'info',
        'action' => 'asignarCompetencia',
        'params' => [
            'competencia_id' => $competenciaId,
            'resultado_id' => $resultadoId
        ]
    ]);
}
```

---

### 4. Sistema Global de Modales de Confirmación (ERP SENA)
**Ubicación**: `resources/js/global-modals.js` + `resources/css/global-modals.css` + `resources/views/vendor/adminlte/page.blade.php`

#### 🎯 Diseño Profesional ERP
Modal pequeño, centrado, enfocado en la acción con identidad visual SENA.

#### 📋 Estructura Mental Correcta
```
[ Ícono ]  Acción clara
Mensaje corto
Objeto afectado (competencia)
[ Cancelar ] [ Acción ]
```

#### ✅ Modal de Asignar Competencia
```
[ 🔗 ]  Asignar competencia
¿Desea asignar esta competencia al programa?

[ 38362 ] [ Competencia ] Modelado de los artefactos del software

[ Cancelar ] [ Asignar ]
```

#### ✅ Modal de Quitar Competencia
```
[ 🔗 ]  Desasociar competencia
¿Desea quitar esta competencia del programa?

[ 38362 ] [ Competencia ] Modelado de los artefactos del software

[ Cancelar ] [ Quitar ]
```

#### 🛠️ Implementación Completa

##### **1. HTML del Modal (en layout principal)**
```html
<!-- En resources/views/vendor/adminlte/page.blade.php -->
<div id="globalConfirmModal">
    <div class="modal-confirm">
        <div class="modal-confirm-icon success">
            <i class="fas fa-link"></i>
        </div>
        <h5 class="modal-confirm-title">Confirmar acción</h5>
        <p class="modal-confirm-text">¿Desea realizar esta acción?</p>
        <div class="modal-confirm-item">
            <span class="code-pill">38362</span>
            <span class="tag">Competencia</span>
            <span>Modelado de los artefactos del software</span>
        </div>
        <div class="modal-confirm-actions">
            <button type="button" class="btn btn-light" onclick="closeConfirmModal()">Cancelar</button>
            <button type="button" class="btn btn-primary btn-confirm" onclick="confirmAction()">Confirmar</button>
        </div>
    </div>
</div>
```

##### **2. CSS Profesional ERP**
```css
/* Modal principal */
#globalConfirmModal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

#globalConfirmModal.show {
    display: flex;
}

/* Contenedor modal */
.modal-confirm {
    background: white;
    border-radius: 8px;
    padding: 24px;
    text-align: center;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

/* Íconos */
.modal-confirm-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 12px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.modal-confirm-icon.success {
    background: #e0f2fe;
    color: #0284c7;
}

.modal-confirm-icon.danger {
    background: #fee2e2;
    color: #b91c1c;
}

/* Texto */
.modal-confirm-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 6px;
    color: #1f2937;
}

.modal-confirm-text {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 16px;
    line-height: 1.4;
}

/* Objeto afectado */
.modal-confirm-item {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 10px;
    font-size: 14px;
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}

.code-pill {
    background: #e5e7eb;
    color: #374151;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    min-width: 50px;
    text-align: center;
}

.tag {
    background: #f3f4f6;
    color: #6b7280;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
}

/* Botones */
.modal-confirm-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
}

.modal-confirm-actions button {
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid;
}

.btn-light {
    background: #f9fafb;
    color: #374151;
    border-color: #d1d5db;
}

.btn-primary {
    background: #0284c7;
    color: white;
    border-color: #0284c7;
}

.btn-danger {
    background: #dc2626;
    color: white;
    border-color: #dc2626;
}
```

##### **3. JavaScript Global**
```javascript
// Variables globales
let modalConfig = null;

// Función principal del modal
function showConfirmModal(title, message, type, action, params, codigo, nombre) {
    modalConfig = {action, params};
    
    const modal = document.getElementById('globalConfirmModal');
    const titleEl = modal.querySelector('.modal-confirm-title');
    const messageEl = modal.querySelector('.modal-confirm-text');
    const iconEl = modal.querySelector('.modal-confirm-icon');
    const codeEl = modal.querySelector('.code-pill');
    const tagEl = modal.querySelector('.tag');
    const nameEl = modal.querySelector('.modal-confirm-item span:last-child');
    const confirmBtn = modal.querySelector('.btn-confirm');
    
    // Configurar contenido
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    // Configurar icono
    iconEl.className = 'modal-confirm-icon';
    switch(type) {
        case 'danger':
            iconEl.classList.add('danger');
            iconEl.innerHTML = '<i class="fas fa-unlink"></i>';
            break;
        case 'info':
        case 'success':
            iconEl.classList.add('success');
            iconEl.innerHTML = '<i class="fas fa-link"></i>';
            break;
    }
    
    // Configurar información
    if (codeEl) codeEl.textContent = codigo;
    if (tagEl) tagEl.textContent = 'Competencia';
    if (nameEl) nameEl.textContent = nombre;
    
    // Configurar botón
    if (confirmBtn) {
        confirmBtn.className = 'btn btn-' + type;
        confirmBtn.textContent = type === 'danger' ? 'Quitar' : 'Asignar';
    }
    
    // Mostrar modal
    modal.style.display = 'block';
    modal.classList.add('show');
}

// Funciones de ayuda
function closeConfirmModal() {
    const modal = document.getElementById('globalConfirmModal');
    modal.style.display = 'none';
    modal.classList.remove('show');
    modalConfig = null;
}

function confirmAction() {
    if (!modalConfig) return;
    
    // Enviar evento a Livewire
    Livewire.dispatch('confirmAction', {
        action: modalConfig.action,
        params: modalConfig.params
    });
    
    closeConfirmModal();
}

// Métodos específicos para competencias
window.confirmarAsociar = function(competenciaId, nombreCompetencia) {
    const partes = nombreCompetencia.split(' - ');
    const codigo = partes[0] || competenciaId;
    const nombre = partes[1] || nombreCompetencia;
    
    showConfirmModal(
        'Asignar competencia',
        '¿Desea asignar esta competencia al programa?',
        'info',
        'asignarCompetencia',
        competenciaId,
        codigo,
        nombre
    );
};

window.confirmarDesasociar = function(competenciaId, nombreCompetencia) {
    const partes = nombreCompetencia.split(' - ');
    const codigo = partes[0] || competenciaId;
    const nombre = partes[1] || nombreCompetencia;
    
    showConfirmModal(
        'Desasociar competencia',
        '¿Desea quitar esta competencia del programa?',
        'danger',
        'desasociarCompetencia',
        competenciaId,
        codigo,
        nombre
    );
};

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeConfirmModal();
        }
    });
    
    // Cerrar al hacer clic fuera
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('globalConfirmModal');
        if (modal && e.target === modal) {
            closeConfirmModal();
        }
    });
});
```

##### **4. Componente Livewire Handler**
```php
<?php

namespace App\Livewire\ResultadosAprendizaje;

use Livewire\Component;
use App\Models\ResultadosAprendizaje;
use App\Models\Competencia;

class GestionarCompetenciasHandler extends Component
{
    public $resultadoId;

    protected $listeners = [
        'confirmAction' => 'handleConfirmedAction',
    ];

    public function mount()
    {
        $this->resultadoId = request()->segment(2);
    }

    public function handleConfirmedAction($action, $params)
    {
        try {
            switch ($action) {
                case 'asignarCompetencia':
                    $this->asignarCompetencia($params);
                    break;
                case 'desasociarCompetencia':
                    $this->desasociarCompetencia($params);
                    break;
            }
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Operación completada correctamente'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al ejecutar la acción: ' . $e->getMessage()
            ]);
        }
    }

    public function asignarCompetencia($competenciaId)
    {
        $resultado = ResultadosAprendizaje::findOrFail($this->resultadoId);
        $resultado->competencias()->attach($competenciaId, [
            'user_create_id' => auth()->id(),
            'user_edit_id' => auth()->id(),
        ]);
    }

    public function desasociarCompetencia($competenciaId)
    {
        $resultado = ResultadosAprendizaje::findOrFail($this->resultadoId);
        $resultado->competencias()->detach($competenciaId);
    }

    public function render()
    {
        return view('livewire.resultados-aprendizaje.gestionar-competencias-handler');
    }
}
```

##### **5. Vista del Handler**
```blade
<div>
    <!-- Componente Livewire para manejar acciones de gestión de competencias -->
    <!-- Este componente no renderiza nada visible, solo maneja eventos -->
</div>
```

##### **6. Uso en Vistas Blade**
```blade
<!-- Botón de agregar -->
<button onclick="confirmarAsociar({{ $competencia->id }}, '{{ $competencia->codigo }} - {{ $competencia->nombre }}')" 
        class="btn-action btn-add"
        title="Asignar competencia">
    <i class="fas fa-plus"></i> Agregar
</button>

<!-- Botón de quitar -->
<button onclick="confirmarDesasociar({{ $competencia->id }}, '{{ $competencia->codigo }} - {{ $competencia->nombre }}')" 
        class="btn-action btn-remove"
        title="Desasociar competencia">
    <i class="fas fa-minus"></i> Quitar
</button>

<!-- Componente Livewire en la vista -->
<livewire:resultados-aprendizaje.gestionar-competencias-handler />
```

##### **7. Configuración Vite**
```javascript
// En vite.config.js
'global_modals_js': 'resources/js/global-modals.js',
'global_modals_css': 'resources/css/global-modals.css',
```

##### **8. Integración en Layout**
```html
<!-- En resources/views/vendor/adminlte/page.blade.php -->
@section('adminlte_css')
    @vite(['resources/css/global-modals.css'])
@endsection

@section('adminlte_js')
    @vite(['resources/js/global-modals.js'])
@endsection
```

#### 🎨 Características Clave

##### **✅ Diseño ERP Profesional**
- **Modal pequeña** (400px max-width)
- **Sin header pesado**
- **Foco total en la acción**
- **Identidad visual SENA**

##### **✅ Sin Confusión Cognitiva**
- **Código numérico** real (38362)
- **Tag contextual** ([Competencia])
- **Nombre completo** visible
- **Jerarquía clara**

##### **✅ UX Optimizada**
- **1 segundo** para entender
- **Acción clara** en título
- **Objeto separado** abajo
- **Botón explícito**

##### **✅ Técnico Robusto**
- **CSS con `!important`** para forzar estilos
- **Fallback múltiple** para selectores
- **Debugging completo** en consola
- **Manejo de errores** integrado

#### 🚀 Beneficios del Sistema

##### **✅ Consistencia Total**
- **Mismo diseño** en todo el sistema
- **Misma experiencia** de usuario
- **Reutilizable** infinitamente

##### **✅ Mantenimiento Fácil**
- **Un solo lugar** para modificar estilos
- **Un solo lugar** para modificar comportamiento
- **Actualización centralizada**

##### **✅ Desarrollo Rápido**
- **1 línea de código** para mostrar modal
- **Sin repetición** de HTML/JS/CSS
- **Integración automática** con Livewire

#### 📋 Regla de Oro del Modal

> **El texto pregunta. El bloque muestra el objeto. El botón ejecuta.**

Esta regla garantiza que cada modal sea:
- **Clara** - La pregunta es explícita
- **Informativa** - El objeto es visible
- **Accionable** - El botón ejecuta la acción

---

## 💡 Tips Adicionales

1. **Siempre usa `$this->dispatch('notify')`** para feedback al usuario
2. **Mantén la estructura de modales** para consistencia
3. **Usa los mismos nombres de eventos** para mantener compatibilidad
4. **Aprovecha los patrones de búsqueda y filtrado**
5. **Mantén el estilo visual consistente** con el resto del sistema
6. **Usa el sistema global de confirmación** para acciones importantes
7. **Documenta tus componentes** siguiendo estos patrones

---

**¡Con esta guía puedes crear cualquier módulo nuevo en minutos usando los componentes ya probados!** 🎉
