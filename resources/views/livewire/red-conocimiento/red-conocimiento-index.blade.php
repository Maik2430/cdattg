<div>
    <!-- Toast Minimalista ERP -->
    <div class="toast toast-minimal">
        <i class="toast-icon"></i>
        <span class="toast-text"></span>
    </div>

    <!-- Barra de herramientas moderna -->
    <div class="toolbar">
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   class="search-input" 
                   placeholder="Buscar por nombre, regional...">
        </div>
        
        <!-- Filtros adicionales -->
        <div class="filters-container">
            <select wire:model.live="statusFilter" class="filter-select">
                <option value="">Todos los estados</option>
                <option value="1">Activos</option>
                <option value="0">Inactivos</option>
            </select>
            
            <select wire:model.live="regionalFilter" class="filter-select">
                <option value="">Todas las regionales</option>
                @foreach ($this->regionales as $regional)
                    <option value="{{ $regional->id }}">{{ $regional->nombre }}</option>
                @endforeach
            </select>
            
            @if ($search || $statusFilter !== '' || $regionalFilter !== '')
                <button wire:click="clearFilters" class="btn-clear-filters" title="Limpiar filtros">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>
        
        <div class="results-selector">
            <select wire:model.live="perPage" class="results-select">
                <option value="10">10 resultados</option>
                <option value="15">15 resultados</option>
                <option value="25">25 resultados</option>
                <option value="50">50 resultados</option>
            </select>
        </div>
        
        @can('CREAR RED CONOCIMIENTO')
            <button wire:click="openCreateModal" class="btn-primary-modern">
                <i class="fas fa-plus"></i>
                Nueva Red
            </button>
        @endcan
    </div>

    <!-- Indicador de carga -->
    <div wire:loading wire:target="search" class="loading-indicator">
        <i class="fas fa-spinner fa-spin"></i>
        Buscando...
    </div>

    <div wire:loading wire:target="statusFilter" class="loading-indicator">
        <i class="fas fa-spinner fa-spin"></i>
        Filtrando por estado...
    </div>

    <div wire:loading wire:target="regionalFilter" class="loading-indicator">
        <i class="fas fa-spinner fa-spin"></i>
        Filtrando por regional...
    </div>

    <div wire:loading wire:target="perPage" class="loading-indicator">
        <i class="fas fa-spinner fa-spin"></i>
        Actualizando resultados...
    </div>

    <!-- Tabla ERP - Solución Definitiva (1 sola tabla) -->
    <div class="table-scroll-wrapper">
        <table class="modern-table">
            <thead>
                <tr>
                    <th class="sortable nombre" wire:click="sortBy('nombre')">
                        Nombre
                        @if ($sortField === 'nombre')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} sort-icon"></i>
                        @endif
                    </th>
                    <th class="regional">Regional</th>
                    <th class="sortable programas-count" wire:click="sortBy('created_at')">
                        Programas
                        @if ($sortField === 'created_at')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} sort-icon"></i>
                        @endif
                    </th>
                    <th class="estado">Estado</th>
                    <th class="th-actions sticky-actions">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($redes as $red)
                    <tr>
                        <td class="nombre fw-medium">{{ $red->nombre }}</td>
                        <td class="regional">
                            @if ($red->regional)
                                <span class="badge-modern badge-info">{{ $red->regional->nombre }}</span>
                            @else
                                <span class="badge-modern badge-secondary">Sin asignar</span>
                            @endif
                        </td>
                        <td class="programas-count">
                            <span class="badge-modern badge-primary">{{ $red->programasFormacion->count() }}</span>
                        </td>
                        <td class="estado">
                            <button
                                wire:click="toggleStatus({{ $red->id }})"
                                wire:loading.attr="disabled"
                                class="badge-toggle {{ $red->status ? 'badge-success' : 'badge-danger' }}"
                                title="Cambiar estado">
                                <i class="fas fa-sync-alt me-1"></i>
                                {{ $red->status ? 'Activo' : 'Inactivo' }}
                            </button>
                        </td>
                        <td class="td-actions sticky-actions">
                            @can('VER RED CONOCIMIENTO')
                                <button wire:click="openShowModal({{ $red->id }})" 
                                        class="btn-action btn-view" 
                                        title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endcan
                            @can('EDITAR RED CONOCIMIENTO')
                                <button wire:click="openEditModal({{ $red->id }})" 
                                        class="btn-action btn-edit" 
                                        title="editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            @endcan
                            @can('ELIMINAR RED CONOCIMIENTO')
                                <button wire:click="confirmDelete({{ $red->id }})" 
                                        class="btn-action btn-delete" 
                                        title="Eliminar red">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-network-wired"></i>
                                </div>
                                <h3>Aún no hay redes de conocimiento</h3>
                                <p>Comienza creando tu primera red de conocimiento para organizar los programas del SENA.</p>
                                <div class="action-hint">Acción recomendada</div>
                                @can('CREAR RED CONOCIMIENTO')
                                    <button wire:click="openCreateModal" class="btn-primary-modern">
                                        <i class="fas fa-plus"></i>
                                        Crear Primera Red
                                    </button>
                                @endcan
                                <div class="action-hint">Tardarás menos de 2 minutos</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
        
        <!-- Paginación (siempre visible) -->
        <div class="pagination-wrapper">
            <div class="pagination-modern">
                <div class="pagination-info">
                    Mostrando {{ $redes->firstItem() ?? 0 }} a {{ $redes->lastItem() ?? 0 }} 
                    de {{ $redes->total() }} resultados
                </div>
                <div class="pagination-links">
                    {{ $redes->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

    <!-- Modal Confirmación Eliminación -->
    @if ($showDeleteModal && $selectedRed)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-sm" style="margin-top: 50px; margin-bottom: 50px;">
                <div class="modal-content">
                    <!-- 1️⃣ Header neutro -->
                    <div class="modal-header-simple">
                        <h5 class="modal-title text-danger">Eliminar red</h5>
                        <button class="modal-close" wire:click="closeDeleteModal">✕</button>
                    </div>
                    
                    <div class="modal-body" style="padding: 1.5rem 1.5rem;">
                        <!-- 2️⃣ Mensaje principal -->
                        <p class="confirm-text">
                            ¿Está seguro de que desea eliminar esta red de conocimiento?
                        </p>
                        
                        <!-- 3️⃣ Información mínima -->
                        <div class="confirm-details">
                            <div><strong>Nombre:</strong> {{ $selectedRed->nombre }}</div>
                            <div><strong>Regional:</strong> {{ $selectedRed->regional->nombre ?? 'Sin asignar' }}</div>
                            <div><strong>Programas:</strong> {{ $selectedRed->programasFormacion->count() }}</div>
                        </div>
                        
                        <!-- 4️⃣ Advertencia discreta -->
                        <p class="confirm-warning">
                            Esta acción es permanente y no se puede deshacer.
                        </p>
                    </div>
                    
                    <!-- 5️⃣ Acciones claras -->
                    <div class="modal-actions">
                        <button class="btn btn-outline-secondary" wire:click="closeDeleteModal">
                            Cancelar
                        </button>
                        <button class="btn btn-danger" wire:click="deleteRed({{ $selectedRed->id }})" 
                                wire:loading.attr="disabled">
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Crear/Editar -->
    @if ($showCreateModal || $showEditModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content modal-erp-container">
                    <div class="modal-header-erp">
                        <h5 class="modal-title-erp">
                            {{ $showCreateModal ? 'Crear Red' : 'Editar Red' }}
                        </h5>
                        <button wire:click="closeCreateModal; closeEditModal" type="button" class="btn-close-erp">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body-erp">
                        @if ($showCreateModal)
                            <livewire:red-conocimiento.red-conocimiento-form />
                        @endif
                        @if ($showEditModal && $selectedRed)
                            <livewire:red-conocimiento.red-conocimiento-form :redId="$selectedRed->id" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Ver Detalles -->
    @if ($showShowModal && $selectedRed)
        <div class="modal-overlay" wire:click="$set('showShowModal', false)">
            <div class="modal-container" wire:click.stop>
                
                <!-- HEADER SIMPLE UNIFICADO -->
                <div class="modal-header-simple">
                    <div>
                        <h4 class="modal-title">{{ $selectedRed->nombre }}</h4>
                        <p class="modal-subtitle">
                            {{ $selectedRed->regional->nombre ?? '' }}
                        </p>
                    </div>

                    <button class="modal-close" wire:click="$set('showShowModal', false)">
                        ✕
                    </button>
                </div>

                <!-- BODY (SCROLL AQUÍ) -->
                <div class="modal-body">
                    <div class="modal-content-wrapper">
                        
                        <!-- Sección: Información General -->
                        <div class="section-card">
                            <h6 class="section-title">Información General</h6>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Nombre</div>
                                    <div class="info-value">{{ $selectedRed->nombre }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Regional</div>
                                    <div class="info-value">{{ $selectedRed->regional->nombre ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección: Programas Asociados -->
                        <div class="section-card">
                            <h6 class="section-title">Programas Asociados ({{ $selectedRed->programasFormacion->count() }})</h6>
                            @if ($selectedRed->programasFormacion->count() > 0)
                                <div class="programs-list">
                                    @foreach ($selectedRed->programasFormacion->take(5) as $programa)
                                        <div class="program-item">
                                            <span class="program-code">{{ $programa->codigo }}</span>
                                            <span class="program-name">{{ $programa->nombre }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($selectedRed->programasFormacion->count() > 5)
                                    <div class="text-muted small mt-2">
                                        ... y {{ $selectedRed->programasFormacion->count() - 5 }} más
                                    </div>
                                @endif
                            @else
                                <p class="text-muted">No hay programas asociados a esta red.</p>
                            @endif
                        </div>
                        
                        <!-- Sección: Estado de la Red -->
                        <div class="section-card">
                            <h6 class="section-title">Estado de la red</h6>
                            <div class="status-section">
                                <div class="status-display">
                                    <span class="badge-status {{ $selectedRed->status ? 'badge-active' : 'badge-inactive' }}">
                                        <i class="fas fa-{{ $selectedRed->status ? 'check' : 'times' }} me-1"></i>
                                        {{ $selectedRed->status ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                <div class="status-description">
                                    Esta red {{ $selectedRed->status ? 'puede' : 'no puede' }} ser usada en nuevos programas
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección: Acciones -->
                        <div class="section-card section-actions">
                            <h6 class="section-title">Acciones</h6>
                            <div class="quick-actions">
                                @can('EDITAR RED CONOCIMIENTO')
                                    <button wire:click="openEditModal({{ $selectedRed->id }})" 
                                            class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>
                                        Editar red
                                    </button>
                                @endcan
                                <button wire:click="toggleStatus({{ $selectedRed->id }})" 
                                        wire:loading.attr="disabled"
                                        class="btn {{ $selectedRed->status ? 'btn-danger' : 'btn-success' }}">
                                    <span wire:loading.remove wire:target="toggleStatus">
                                        <i class="fas fa-sync-alt me-2"></i>
                                        {{ $selectedRed->status ? 'Desactivar red' : 'Activar red' }}
                                    </span>
                                    <span wire:loading wire:target="toggleStatus">
                                        <i class="fas fa-spinner fa-spin me-2"></i>
                                        Procesando...
                                    </span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Sección: Auditoría -->
                        <div class="section-card">
                            <h6 class="section-title">Auditoría</h6>
                            <div class="audit-section">
                                <div class="audit-block">
                                    <div class="audit-label">Creado por</div>
                                    <div class="audit-info">
                                        <div class="audit-user">{{ $selectedRed->userCreated->name ?? 'Sistema' }}</div>
                                        <div class="audit-date">{{ $selectedRed->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                                <div class="audit-block">
                                    <div class="audit-label">Última edición</div>
                                    <div class="audit-info">
                                        <div class="audit-user">{{ $selectedRed->userEdited->name ?? 'Sin edición' }}</div>
                                        <div class="audit-date">{{ $selectedRed->updated_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>
    @endif

    <!-- CSS para modal de eliminación -->
    <style>
    /* 🎨 CSS para modal de eliminación limpio y profesional */
    .modal-header-simple {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        padding: 4px;
        color: #6b7280;
    }

    .modal-close:hover {
        color: #374151;
    }

    .confirm-text {
        font-size: 16px;
        font-weight: 500;
        margin-top: 8px;
        margin-bottom: 16px;
        color: #111827;
    }

    .confirm-details {
        font-size: 14px;
        color: #374151;
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding-left: 8px;
        border-left: 3px solid #e5e7eb;
        margin: 12px 0;
    }

    .confirm-warning {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 20px;
        font-style: italic;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 20px;
        border-top: 1px solid #e5e7eb;
    }

    .modal-actions .btn {
        padding: 8px 16px;
        font-weight: 500;
        border-radius: 6px;
    }

    .btn-outline-secondary {
        background: white;
        border: 1px solid #d1d5db;
        color: #374151;
    }

    .btn-outline-secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    .btn-danger {
        background: #dc2626;
        border: 1px solid #dc2626;
        color: white;
    }

    .btn-danger:hover {
        background: #b91c1c;
        border-color: #b91c1c;
    }

    .btn-danger:disabled {
        background: #9ca3af;
        border-color: #9ca3af;
        cursor: not-allowed;
    }
    </style>

    <!-- JavaScript -->
    <script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('notify', (payload) => {
            const data = payload[0];
            showToast(data.message, data.type);
        });
        
        Livewire.on('redCreada', function() {
            showToast('Red de conocimiento creada correctamente', 'success');
        });
        
        Livewire.on('redActualizada', function() {
            showToast('Red de conocimiento actualizada correctamente', 'success');
        });
        
        Livewire.on('redEliminada', function() {
            showToast('Red de conocimiento eliminada correctamente', 'warning');
        });
        
        // Función para mostrar toast minimalista ERP
        function showToast(message, type = 'info') {
            const toast = document.querySelector('.toast-minimal');
            const icon = toast.querySelector('.toast-icon');
            const text = toast.querySelector('.toast-text');

            const icons = {
                success: '✓',
                warning: '↻',
                error: '✕',
                info: 'ℹ'
            };

            toast.className = `toast-minimal show ${type}`;
            icon.textContent = icons[type] ?? 'ℹ';
            text.textContent = message;

            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }
    });
    </script>
</div>
