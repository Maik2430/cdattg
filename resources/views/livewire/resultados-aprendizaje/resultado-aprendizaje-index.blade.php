<div>
    <!-- Barra de herramientas moderna -->
    <div class="toolbar">
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   class="search-input" 
                   placeholder="Buscar por código, nombre...">
        </div>
        
        <!-- Filtros adicionales -->
        <div class="filters-container">
            <select wire:model.live="statusFilter" class="filter-select">
                <option value="">Todos los estados</option>
                <option value="1">Activos</option>
                <option value="0">Inactivos</option>
            </select>
            
            <select wire:model.live="competenciaFilter" class="filter-select">
                <option value="">Todas las competencias</option>
                @foreach($competencias as $competencia)
                    <option value="{{ $competencia->id }}">{{ Str::limit($competencia->nombre, 25) }}</option>
                @endforeach
            </select>
            
            @if ($search || $statusFilter !== '' || $competenciaFilter !== '')
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
        
        @can('CREAR RESULTADO APRENDIZAJE')
            <button wire:click="openCreateModal" class="btn-primary-modern">
                <i class="fas fa-plus"></i>
                Nuevo Resultado
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

    <div wire:loading wire:target="competenciaFilter" class="loading-indicator">
        <i class="fas fa-spinner fa-spin"></i>
        Filtrando por competencia...
    </div>

    <div wire:loading wire:target="perPage" class="loading-indicator">
        <i class="fas fa-spinner fa-spin"></i>
        Actualizando resultados...
    </div>

    <div wire:loading wire:target="page" class="loading-indicator">
        <i class="fas fa-spinner fa-spin"></i>
        Cargando página...
    </div>

    <!-- Tabla ERP -->
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
                    <th class="duracion">Duración</th>
                    <th class="competencias">Competencias</th>
                    <th class="guias">Guías</th>
                    <th class="estado">Estado</th>
                    <th class="th-actions sticky-actions">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($resultados as $resultado)
                    <tr>
                        <td class="codigo fw-medium">{{ $resultado->codigo }}</td>
                        <td class="nombre">{{ Str::limit($resultado->nombre, 50) }}</td>
                        <td class="duracion">
                            @if($resultado->duracion)
                                <span class="badge-modern badge-info">{{ $this->formatearHoras($resultado->duracion) }}h</span>
                            @else
                                <span class="badge-modern badge-secondary">N/A</span>
                            @endif
                        </td>
                        <td class="competencias">
                            <span class="badge-modern badge-primary">{{ $resultado->competencias->count() }}</span>
                        </td>
                        <td class="guias">
                            <span class="badge-modern badge-warning">{{ $resultado->guiasAprendizaje->count() }}</span>
                        </td>
                        <td class="estado">
                            <button
                                wire:click="toggleStatus({{ $resultado->id }})"
                                wire:loading.attr="disabled"
                                class="badge-toggle {{ $resultado->status ? 'badge-success' : 'badge-danger' }}"
                                title="Cambiar estado">
                                <i class="fas fa-sync-alt me-1"></i>
                                {{ $resultado->status ? 'Activo' : 'Inactivo' }}
                            </button>
                        </td>
                        <td class="td-actions sticky-actions">
                            @can('VER RESULTADO APRENDIZAJE')
                                <button wire:click="openShowModal({{ $resultado->id }})" 
                                        class="btn-action btn-view" 
                                        title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endcan
                            @can('EDITAR RESULTADO APRENDIZAJE')
                                <button wire:click="openEditModal({{ $resultado->id }})" 
                                        class="btn-action btn-edit" 
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            @endcan
                            @can('GESTIONAR COMPETENCIAS RESULTADO APRENDIZAJE')
                                <a href="{{ route('resultados-aprendizaje.gestionarCompetencias', $resultado->id) }}" 
                                   class="btn-action btn-competencias" 
                                   title="Gestionar competencias">
                                    <i class="fas fa-link"></i>
                                </a>
                            @endcan
                            @can('ELIMINAR RESULTADO APRENDIZAJE')
                                <button wire:click="confirmDelete({{ $resultado->id }})" 
                                        class="btn-action btn-delete" 
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            @if($resultados->total() > 0)
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <h3>No hay resultados en esta página</h3>
                                    <p>Esta página está vacía. Intenta con otra página o ajusta los filtros.</p>
                                    <div class="action-hint">Total: {{ $resultados->total() }} resultados</div>
                                    <div class="action-hint">Resultados por página: {{ $resultados->perPage() }}</div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <h3>Aún no hay resultados de aprendizaje</h3>
                                    <p>Comienza creando tu primer resultado de aprendizaje para organizar las guías del SENA.</p>
                                    <div class="action-hint">Acción recomendada</div>
                                    @can('CREAR RESULTADO APRENDIZAJE')
                                        <button wire:click="openCreateModal" class="btn-primary-modern">
                                            <i class="fas fa-plus"></i>
                                            Crear Primer Resultado
                                        </button>
                                    @endcan
                                    <div class="action-hint">Tardarás menos de 2 minutos</div>
                                </div>
                            @endif
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
                Mostrando {{ $resultados->firstItem() ?? 0 }} a {{ $resultados->lastItem() ?? 0 }} 
                de {{ $resultados->total() }} resultados
            </div>
            <div class="pagination-links">
                {{ $resultados->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <!-- Modal Crear/Editar -->
    @if ($showCreateModal || $showEditModal)
        <div class="modal-overlay" wire:click="$set('showCreateModal', false); $set('showEditModal', false)">
            <div class="modal-container" wire:click.stop>
                
                <!-- Header Simple Unificado -->
                <div class="modal-header-simple">
                    <div>
                        <h4 class="modal-title">{{ $showEditModal ? 'Editar Resultado de Aprendizaje' : 'Nuevo Resultado de Aprendizaje' }}</h4>
                        <p class="modal-subtitle">
                            {{ $showEditModal ? 'Modifica los datos del resultado' : 'Completa los datos para crear un nuevo resultado' }}
                        </p>
                    </div>

                    <button class="modal-close" wire:click="$set('showCreateModal', false); $set('showEditModal', false)">✕</button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="modal-content-wrapper">
                        <livewire:resultados-aprendizaje.resultado-aprendizaje-form 
                            :is-edit="$showEditModal" 
                            :resultado-id="$selectedResultado?->id" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Ver Detalles -->
    @if ($showShowModal && $selectedResultado)
        <div class="modal-overlay" wire:click="$set('showShowModal', false)">
            <div class="modal-container" wire:click.stop>
                
                <!-- Header Simple Unificado -->
                <div class="modal-header-simple">
                    <div>
                        <h4 class="modal-title">{{ $selectedResultado->codigo }} - {{ $selectedResultado->nombre }}</h4>
                        <p class="modal-subtitle">
                            Resultado de aprendizaje del SENA
                        </p>
                    </div>

                    <button class="modal-close" wire:click="$set('showShowModal', false)">✕</button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="modal-content-wrapper">
                        
                        <!-- Sección: Información General -->
                        <div class="section-card">
                            <h6 class="section-title">Información General</h6>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Código</div>
                                    <div class="info-value">{{ $selectedResultado->codigo }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Nombre</div>
                                    <div class="info-value">{{ $selectedResultado->nombre }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Duración</div>
                                    <div class="info-value">
                                        @if($selectedResultado->duracion)
                                            {{ $this->formatearHoras($selectedResultado->duracion) }} horas
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Estado</div>
                                    <div class="info-value">
                                        <span class="badge-status {{ $selectedResultado->status ? 'badge-active' : 'badge-inactive' }}">
                                            <i class="fas fa-{{ $selectedResultado->status ? 'check' : 'times' }} me-1"></i>
                                            {{ $selectedResultado->status ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección: Competencias Asociadas -->
                        <div class="section-card">
                            <h6 class="section-title">Competencias Asociadas ({{ $selectedResultado->competencias->count() }})</h6>
                            @if ($selectedResultado->competencias->count() > 0)
                                <div class="programs-list">
                                    @foreach ($selectedResultado->competencias->take(5) as $competencia)
                                        <div class="program-item">
                                            <span class="program-code">{{ $competencia->codigo }}</span>
                                            <span class="program-name">{{ $competencia->nombre }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($selectedResultado->competencias->count() > 5)
                                    <div class="text-muted small mt-2">
                                        ... y {{ $selectedResultado->competencias->count() - 5 }} más
                                    </div>
                                @endif
                            @else
                                <p class="text-muted">No hay competencias asociadas a este resultado.</p>
                            @endif
                        </div>
                        
                        <!-- Sección: Guías de Aprendizaje -->
                        <div class="section-card">
                            <h6 class="section-title">Guías de Aprendizaje ({{ $selectedResultado->guiasAprendizaje->count() }})</h6>
                            @if ($selectedResultado->guiasAprendizaje->count() > 0)
                                <div class="results-summary">
                                    <div class="summary-item">
                                        <span class="summary-value">{{ $selectedResultado->guiasAprendizaje->count() }}</span>
                                        <span class="summary-label">guías creadas</span>
                                    </div>
                                </div>
                                
                                <div class="results-list-small">
                                    @foreach($selectedResultado->guiasAprendizaje->take(3) as $guia)
                                        <div class="result-item">
                                            <div class="result-code">{{ $guia->codigo ?? 'N/A' }}</div>
                                            <div class="result-name">{{ Str::limit($guia->nombre ?? 'Sin nombre', 40) }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($selectedResultado->guiasAprendizaje->count() > 3)
                                    <div class="text-muted small mt-2">
                                        ... y {{ $selectedResultado->guiasAprendizaje->count() - 3 }} más
                                    </div>
                                @endif
                            @else
                                <div class="empty-section">
                                    <div class="empty-icon">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <p class="empty-text">No hay guías de aprendizaje asociadas</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Sección: Acciones -->
                        <div class="section-card section-actions">
                            <h6 class="section-title">Acciones</h6>
                            <div class="quick-actions">
                                @can('EDITAR RESULTADO APRENDIZAJE')
                                    <button wire:click="openEditModal({{ $selectedResultado->id }})" 
                                            class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>
                                        Editar resultado
                                    </button>
                                @endcan
                                <button wire:click="toggleStatus({{ $selectedResultado->id }})" 
                                        wire:loading.attr="disabled"
                                        class="btn btn-{{ $selectedResultado->status ? 'danger' : 'success' }}">
                                    <i class="fas fa-{{ $selectedResultado->status ? 'times' : 'check' }} me-2"></i>
                                    {{ $selectedResultado->status ? 'Desactivar' : 'Activar' }}
                                </button>
                                <a href="{{ route('resultados-aprendizaje.competencias', $selectedResultado->id) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-link me-2"></i>
                                    Gestionar competencias
                                </a>
                            </div>
                        </div>
                                @if($selectedResultado->userEdit)
                                    <div class="audit-block">
                                        <div class="audit-label">Última modificación</div>
                                        <div class="audit-info">
                                            <div class="audit-user">{{ $selectedResultado->userEdit->name }}</div>
                                            <div class="audit-date">{{ $selectedResultado->updated_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Confirmación Eliminación -->
    @if ($showDeleteModal && $selectedResultado)
        <div class="modal-overlay" wire:click="$set('showDeleteModal', false)">
            <div class="modal-container modal-sm" wire:click.stop>
                
                <!-- Header Simple Unificado -->
                <div class="modal-header-simple">
                    <div>
                        <h4 class="modal-title text-danger">Eliminar resultado de aprendizaje</h4>
                        <p class="modal-subtitle">Esta acción no se puede deshacer</p>
                    </div>

                    <button class="modal-close" wire:click="$set('showDeleteModal', false)">✕</button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="modal-content-wrapper">
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>¿Está seguro de que desea eliminar este resultado?</strong>
                                <p class="mb-0 mt-1">
                                    Código: <strong>{{ $selectedResultado->codigo }}</strong><br>
                                    Nombre: <strong>{{ $selectedResultado->nombre }}</strong>
                                </p>
                            </div>
                        </div>
                        
                        @if($selectedResultado->guiasAprendizaje->count() > 0)
                            <div class="alert alert-danger mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>No se puede eliminar.</strong> Este resultado tiene {{ $selectedResultado->guiasAprendizaje->count() }} guía(s) asociada(s).
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer-erp">
                    <div class="footer-actions">
                        <button wire:click="$set('showDeleteModal', false)" class="btn-erp btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </button>
                        @if($selectedResultado->guiasAprendizaje->count() == 0)
                            <button wire:click="deleteResultado({{ $selectedResultado->id }})" 
                                    class="btn-erp btn-danger">
                                <i class="fas fa-trash"></i>
                                Eliminar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
