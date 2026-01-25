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
                   placeholder="Buscar por código, nombre...">
        </div>
        
        <div class="results-selector">
            <select wire:model="perPage" class="results-select">
                <option value="10">10 resultados</option>
                <option value="15">15 resultados</option>
                <option value="25">25 resultados</option>
                <option value="50">50 resultados</option>
            </select>
        </div>
        
        @can('programa.create')
            <button wire:click="openCreateModal" class="btn-primary-modern">
                <i class="fas fa-plus"></i>
                Nuevo Programa
            </button>
        @endcan
    </div>

    <!-- Tabla ERP - Solución Definitiva (1 sola tabla) -->
    <div class="table-scroll-wrapper">
        <table class="modern-table">
            <thead>
                <tr>
                    <th class="sortable codigo" wire:click="sortBy('codigo')">
                        Código
                        @if ($sortField === 'codigo')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} sort-icon"></i>
                        @endif
                    </th>
                    <th class="sortable nombre" wire:click="sortBy('nombre')">
                        Nombre
                        @if ($sortField === 'nombre')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} sort-icon"></i>
                        @endif
                    </th>
                    <th class="red">Red de Conocimiento</th>
                    <th class="nivel">Nivel</th>
                    <th class="sortable horas-total" wire:click="sortBy('horas_totales')">
                        Horas Totales
                        @if ($sortField === 'horas_totales')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} sort-icon"></i>
                        @endif
                    </th>
                    <th class="estado">Estado</th>
                    <th class="th-actions sticky-actions">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($programas as $programa)
                    <tr>
                        <td class="codigo">
                            <span class="badge-modern badge-primary">{{ $programa->codigo }}</span>
                        </td>
                        <td class="nombre fw-medium">{{ $programa->nombre }}</td>
                        <td class="red">
                            @if ($programa->redConocimiento)
                                <span class="badge-modern badge-info">{{ $programa->redConocimiento->nombre }}</span>
                            @else
                                <span class="badge-modern badge-secondary">Sin asignar</span>
                            @endif
                        </td>
                        <td class="nivel">
                            @if ($programa->nivelFormacion)
                                <span class="badge-modern badge-success">{{ $programa->nivelFormacion->name }}</span>
                            @else
                                <span class="badge-modern badge-warning">Sin asignar</span>
                            @endif
                        </td>
                        <td class="horas-total">
                            <span class="badge-modern badge-primary">{{ $programa->horas_totales }}h</span>
                        </td>
                        <td class="estado">
                            <button
                                wire:click="toggleStatus({{ $programa->id }})"
                                wire:loading.attr="disabled"
                                class="badge-toggle {{ (int) $programa->status === 1 ? 'badge-success' : 'badge-danger' }}"
                                title="Cambiar estado">
                                <i class="fas fa-sync-alt me-1"></i>
                                {{ (int) $programa->status === 1 ? 'Activo' : 'Inactivo' }}
                            </button>
                        </td>
                        <td class="td-actions sticky-actions">
                            @can('programa.show')
                                <button wire:click="openShowModal({{ $programa->id }})" 
                                        class="btn-action btn-view" 
                                        title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endcan
                            @can('programa.edit')
                                <button wire:click="openEditModal({{ $programa->id }})" 
                                        class="btn-action btn-edit" 
                                        title="editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            @endcan
                            @can('programa.delete')
                                <button wire:click="confirmDelete({{ $programa->id }})" 
                                        class="btn-action btn-delete" 
                                        title="Eliminar programa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <h3>Aún no hay programas de formación</h3>
                                <p>Comienza creando tu primer programa de formación para gestionar la oferta educativa del SENA.</p>
                                <div class="action-hint">Acción recomendada</div>
                                @can('programa.create')
                                    <button wire:click="openCreateModal" class="btn-primary-modern">
                                        <i class="fas fa-plus"></i>
                                        Crear Primer Programa
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
                    Mostrando {{ $programas->firstItem() ?? 0 }} a {{ $programas->lastItem() ?? 0 }} 
                    de {{ $programas->total() }} resultados
                </div>
                <div class="pagination-links">
                    {{ $programas->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

    <!-- Modal Confirmación Eliminación -->
    @if ($showDeleteModal && $selectedPrograma)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-sm" style="margin-top: 50px; margin-bottom: 50px;">
                <div class="modal-content">
                    <!-- 1️⃣ Header neutro -->
                    <div class="modal-header-simple">
                        <h5 class="modal-title text-danger">Eliminar programa</h5>
                        <button class="modal-close" wire:click="closeDeleteModal">✕</button>
                    </div>
                    
                    <div class="modal-body" style="padding: 1.5rem 1.5rem;">
                        <!-- 2️⃣ Mensaje principal -->
                        <p class="confirm-text">
                            ¿Está seguro de que desea eliminar este programa?
                        </p>
                        
                        <!-- 3️⃣ Información mínima -->
                        <div class="confirm-details">
                            <div><strong>Código:</strong> {{ $selectedPrograma->codigo }}</div>
                            <div><strong>Programa:</strong> {{ $selectedPrograma->nombre }}</div>
                            <div><strong>Red:</strong> {{ $selectedPrograma->redConocimiento->nombre ?? 'Sin asignar' }}</div>
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
                        <button class="btn btn-danger" wire:click="deletePrograma({{ $selectedPrograma->id }})" 
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
                            {{ $showCreateModal ? 'Crear Programa' : 'Editar Programa' }}
                        </h5>
                        <button wire:click="closeCreateModal; closeEditModal" type="button" class="btn-close-erp">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body-erp">
                        @if ($showCreateModal)
                            <livewire:programas.programa-form />
                        @endif
                        @if ($showEditModal && $selectedPrograma)
                            <livewire:programas.programa-form :programaId="$selectedPrograma->id" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Ver Detalles -->
    @if ($showShowModal && $selectedPrograma)
        <div class="modal-overlay" wire:click="$set('showShowModal', false)">
            <div class="modal-container" wire:click.stop>
                
                <!-- HEADER SIMPLE UNIFICADO -->
                <div class="modal-header-simple">
                    <div>
                        <h4 class="modal-title">{{ $selectedPrograma->nombre }}</h4>
                        <p class="modal-subtitle">
                            <span class="code-pill">{{ $selectedPrograma->codigo }}</span>
                            {{ $selectedPrograma->redConocimiento->nombre ?? '' }} · {{ $selectedPrograma->nivelFormacion->name ?? '' }}
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
                                    <div class="info-label">Código</div>
                                    <div class="info-value">{{ $selectedPrograma->codigo }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Red de Conocimiento</div>
                                    <div class="info-value">{{ $selectedPrograma->redConocimiento->nombre ?? '' }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Nivel de Formación</div>
                                    <div class="info-value">{{ $selectedPrograma->nivelFormacion->name ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección: Distribución de Horas -->
                        <div class="section-card">
                            <h6 class="section-title">Distribución de horas</h6>
                            
                            <div class="hours-inline">
                                <div class="hour-inline-item">
                                    <span class="hour-icon">⏱</span>
                                    <span class="hour-label">Total</span>
                                    <strong class="hour-value">{{ $selectedPrograma->horas_totales }}h</strong>
                                </div>

                                <div class="hour-inline-item">
                                    <span class="hour-icon">📘</span>
                                    <span class="hour-label">Lectiva</span>
                                    <strong class="hour-value">{{ $selectedPrograma->horas_etapa_lectiva }}h</strong>
                                </div>

                                <div class="hour-inline-item">
                                    <span class="hour-icon">🏭</span>
                                    <span class="hour-label">Productiva</span>
                                    <strong class="hour-value">{{ $selectedPrograma->horas_etapa_productiva }}h</strong>
                                </div>

                                @if (($selectedPrograma->horas_etapa_lectiva + $selectedPrograma->horas_etapa_productiva) == $selectedPrograma->horas_totales)
                                    <div class="hour-inline-status">
                                        ✓ Distribución válida
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Sección: Estado del Programa -->
                        <div class="section-card">
                            <h6 class="section-title">Estado del programa</h6>
                            <div class="status-section">
                                <div class="status-display">
                                    <span class="badge-status {{ (int) $selectedPrograma->status === 1 ? 'badge-active' : 'badge-inactive' }}">
                                        <i class="fas fa-{{ (int) $selectedPrograma->status === 1 ? 'check' : 'times' }} me-1"></i>
                                        {{ (int) $selectedPrograma->status === 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                <div class="status-description">
                                    Este programa {{ (int) $selectedPrograma->status === 1 ? 'puede' : 'no puede' }} ser usado en fichas de formación activas
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección: Acciones -->
                        <div class="section-card section-actions">
                            <h6 class="section-title">Acciones</h6>
                            <div class="quick-actions">
                                @can('programa.edit')
                                    <button wire:click="openEditModal({{ $selectedPrograma->id }})" 
                                            class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>
                                        Editar programa
                                    </button>
                                @endcan
                                <button wire:click="toggleStatus({{ $selectedPrograma->id }}") 
                                        class="btn {{ (int) $selectedPrograma->status === 1 ? 'btn-danger' : 'btn-success' }}">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    {{ (int) $selectedPrograma->status === 1 ? 'Desactivar programa' : 'Activar programa' }}
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
                                        <div class="audit-user">{{ $selectedPrograma->userCreated->name ?? 'Sistema' }}</div>
                                        <div class="audit-date">{{ $selectedPrograma->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                                <div class="audit-block">
                                    <div class="audit-label">Última edición</div>
                                    <div class="audit-info">
                                        <div class="audit-user">{{ $selectedPrograma->userEdited->name ?? 'Sin edición' }}</div>
                                        <div class="audit-date">{{ $selectedPrograma->updated_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Competencias Asociadas -->
                        @if ($selectedPrograma->competencias->count() > 0)
                            <div class="section-card">
                                <h6 class="section-title">Competencias Asociadas ({{ $selectedPrograma->competencias->count() }})</h6>
                                <div class="competencies-list">
                                    @foreach ($selectedPrograma->competencias->take(3) as $competencia)
                                        <div class="competency-item">
                                            <span class="competency-code">{{ $competencia->codigo }}</span>
                                            <span class="competency-name">{{ $competencia->nombre }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($selectedPrograma->competencias->count() > 3)
                                    <div class="text-muted small mt-2">
                                        ... y {{ $selectedPrograma->competencias->count() - 3 }} más
                                    </div>
                                @endif
                            </div>
                        @endif
                        
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
        
        Livewire.on('programaCreado', function() {
            showToast('Programa creado correctamente', 'success');
        });
        
        Livewire.on('programaActualizado', function() {
            showToast('Programa actualizado correctamente', 'success');
        });
        
        Livewire.on('programaEliminado', function() {
            showToast('Programa eliminado correctamente', 'warning');
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
