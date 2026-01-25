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
                   placeholder="Buscar por código, nombre, descripción...">
        </div>
        
        <!-- Filtros adicionales -->
        <div class="filters-container">
            <select wire:model.live="statusFilter" class="filter-select">
                <option value="">Todos los estados</option>
                <option value="1">Activas</option>
                <option value="0">Inactivas</option>
            </select>
            
            <select wire:model.live="vigenciaFilter" class="filter-select">
                <option value="">Todas las vigencias</option>
                <option value="vigentes">Vigentes</option>
                <option value="no_vigentes">No vigentes</option>
            </select>
            
            @if ($search || $statusFilter !== '' || $vigenciaFilter !== '')
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
        
        @can('CREAR COMPETENCIA')
            <button wire:click="openCreateModal" class="btn-primary-modern">
                <i class="fas fa-plus"></i>
                Nueva Competencia
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

    <div wire:loading wire:target="vigenciaFilter" class="loading-indicator">
        <i class="fas fa-spinner fa-spin"></i>
        Filtrando por vigencia...
    </div>

    <div wire:loading wire:target="perPage" class="loading-indicator">
        <i class="fas fa-spinner fa-spin"></i>
        Actualizando resultados...
    </div>

    <div wire:loading wire:target="page" class="loading-indicator">
        <i class="fas fa-spinner fa-spin"></i>
        Cargando página...
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
                    <th class="descripcion">Descripción</th>
                    <th class="duracion">Duración</th>
                    <th class="vigencia">Vigencia</th>
                    <th class="programas">Programas</th>
                    <th class="estado">Estado</th>
                    <th class="th-actions sticky-actions">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($competencias as $competencia)
                    <tr>
                        <td class="codigo fw-medium">{{ $competencia->codigo }}</td>
                        <td class="nombre">{{ Str::limit($competencia->nombre, 50) }}</td>
                        <td class="descripcion">{{ Str::limit($competencia->descripcion, 60) }}</td>
                        <td class="duracion">
                            @if($competencia->duracion)
                                <span class="badge-modern badge-info">{{ number_format($competencia->duracion, 0) }}h</span>
                            @else
                                <span class="badge-modern badge-secondary">N/A</span>
                            @endif
                        </td>
                        <td class="vigencia">
                            @if($competencia->estaVigente())
                                <span class="badge-modern badge-success">Vigente</span>
                            @else
                                <span class="badge-modern badge-warning">No vigente</span>
                            @endif
                        </td>
                        <td class="programas">
                            <span class="badge-modern badge-primary">{{ $competencia->programasFormacion->count() }}</span>
                        </td>
                        <td class="estado">
                            <button
                                wire:click="toggleStatus({{ $competencia->id }})"
                                wire:loading.attr="disabled"
                                class="badge-toggle {{ $competencia->status ? 'badge-success' : 'badge-danger' }}"
                                title="Cambiar estado">
                                <i class="fas fa-sync-alt me-1"></i>
                                {{ $competencia->status ? 'Activa' : 'Inactiva' }}
                            </button>
                        </td>
                        <td class="td-actions sticky-actions">
                            @can('VER COMPETENCIA')
                                <button wire:click="openShowModal({{ $competencia->id }})" 
                                        class="btn-action btn-view" 
                                        title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endcan
                            @can('GESTIONAR RESULTADOS COMPETENCIA')
                                <a href="{{ route('competencias.gestionarResultados', $competencia->id) }}" 
                                   class="btn-action btn-info" 
                                   title="Gestionar Resultados">
                                    <i class="fas fa-tasks"></i>
                                </a>
                            @endcan
                            @can('EDITAR COMPETENCIA')
                                <button wire:click="openEditModal({{ $competencia->id }})" 
                                        class="btn-action btn-edit" 
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            @endcan
                            @can('ELIMINAR COMPETENCIA')
                                <button wire:click="confirmDelete({{ $competencia->id }})" 
                                        class="btn-action btn-delete" 
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            @if($competencias->total() > 0)
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <h3>No hay competencias en esta página</h3>
                                    <p>Esta página está vacía. Intenta con otra página o ajusta los filtros.</p>
                                    <div class="action-hint">Total: {{ $competencias->total() }} competencias</div>
                                    <div class="action-hint">Resultados por página: {{ $competencias->perPage() }}</div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <h3>Aún no hay competencias</h3>
                                    <p>Comienza creando tu primera competencia para organizar los programas del SENA.</p>
                                    <div class="action-hint">Acción recomendada</div>
                                    @can('CREAR COMPETENCIA')
                                        <button wire:click="openCreateModal" class="btn-primary-modern">
                                            <i class="fas fa-plus"></i>
                                            Crear Primera Competencia
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
                    Mostrando {{ $competencias->firstItem() ?? 0 }} a {{ $competencias->lastItem() ?? 0 }} 
                    de {{ $competencias->total() }} resultados
                </div>
                <div class="pagination-links">
                    {{ $competencias->links('pagination::bootstrap-4') }}
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
                        <h4 class="modal-title">{{ $showEditModal ? 'Editar Competencia' : 'Nueva Competencia' }}</h4>
                        <p class="modal-subtitle">
                            {{ $showEditModal ? 'Modifica los datos de la competencia' : 'Completa los datos para crear una nueva competencia' }}
                        </p>
                    </div>

                    <button class="modal-close" wire:click="$set('showCreateModal', false); $set('showEditModal', false)">✕</button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="modal-content-wrapper">
                        <livewire:competencias.competencia-form 
                            :is-edit="$showEditModal" 
                            :competencia-id="$selectedCompetencia?->id" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Ver Detalles -->
    @if ($showShowModal && $selectedCompetencia)
        <div class="modal-overlay" wire:click="$set('showShowModal', false)">
            <div class="modal-container" wire:click.stop>
                
                <!-- Header Simple Unificado -->
                <div class="modal-header-simple">
                    <div>
                        <h4 class="modal-title">{{ $selectedCompetencia->codigo }} - {{ $selectedCompetencia->nombre }}</h4>
                        <p class="modal-subtitle">
                            {{ Str::limit($selectedCompetencia->descripcion, 100) }}
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
                                    <div class="info-value">{{ $selectedCompetencia->codigo }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Nombre</div>
                                    <div class="info-value">{{ $selectedCompetencia->nombre }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Descripción</div>
                                    <div class="info-value">{{ $selectedCompetencia->descripcion }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Duración</div>
                                    <div class="info-value">{{ $selectedCompetencia->duracionFormateada }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Fecha Inicio</div>
                                    <div class="info-value">{{ $selectedCompetencia->fechaInicioFormateada }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Fecha Fin</div>
                                    <div class="info-value">{{ $selectedCompetencia->fechaFinFormateada }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección: Programas Asociados -->
                        <div class="section-card">
                            <h6 class="section-title">Programas Asociados ({{ $selectedCompetencia->programasFormacion->count() }})</h6>
                            @if ($selectedCompetencia->programasFormacion->count() > 0)
                                <div class="programs-list">
                                    @foreach ($selectedCompetencia->programasFormacion->take(5) as $programa)
                                        <div class="program-item">
                                            <span class="program-code">{{ $programa->codigo }}</span>
                                            <span class="program-name">{{ $programa->nombre }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($selectedCompetencia->programasFormacion->count() > 5)
                                    <div class="text-muted small mt-2">
                                        ... y {{ $selectedCompetencia->programasFormacion->count() - 5 }} más
                                    </div>
                                @endif
                            @else
                                <p class="text-muted">No hay programas asociados a esta competencia.</p>
                            @endif
                        </div>
                        
                        <!-- Sección: Estado de la Competencia -->
                        <div class="section-card">
                            <h6 class="section-title">Estado de la competencia</h6>
                            <div class="status-section">
                                <div class="status-display">
                                    <span class="badge-status {{ $selectedCompetencia->status ? 'badge-active' : 'badge-inactive' }}">
                                        <i class="fas fa-{{ $selectedCompetencia->status ? 'check' : 'times' }} me-1"></i>
                                        {{ $selectedCompetencia->status ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>
                                <div class="status-description">
                                    Esta competencia {{ $selectedCompetencia->status ? 'puede' : 'no puede' }} ser usada en nuevos programas
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección: Resultados de Aprendizaje Asociados -->
                        <div class="section-card">
                            <h6 class="section-title">Resultados de Aprendizaje Asociados</h6>
                            @php
                                $resultadosAsociados = $selectedCompetencia->resultadosAprendizaje()->get();
                                $duracionTotal = $resultadosAsociados->sum(function($resultado) {
                                    return $resultado->pivot->duracion ?? 0;
                                });
                            @endphp
                            
                            @if($resultadosAsociados->isEmpty())
                                <div class="empty-section">
                                    <div class="empty-icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <p class="empty-text">No hay resultados de aprendizaje asociados</p>
                                </div>
                            @else
                                <div class="results-summary">
                                    <div class="summary-item">
                                        <span class="summary-value">{{ $resultadosAsociados->count() }}</span>
                                        <span class="summary-label">resultados</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-value">{{ $this->formatearHoras($duracionTotal) }} h</span>
                                        <span class="summary-label">duración total</span>
                                    </div>
                                </div>
                                
                                <div class="results-list-small">
                                    @foreach($resultadosAsociados as $resultado)
                                        <div class="result-item">
                                            <div class="result-code">{{ $resultado->codigo }}</div>
                                            <div class="result-name">{{ $resultado->nombre }}</div>
                                            <div class="result-duration">{{ $this->formatearHoras($resultado->pivot->duracion ?? 0) }} hrs</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        <!-- Sección: Vigencia -->
                        <div class="section-card">
                            <h6 class="section-title">Vigencia</h6>
                            <div class="status-section">
                                <div class="status-display">
                                    <span class="badge-status {{ $selectedCompetencia->estaVigente() ? 'badge-active' : 'badge-inactive' }}">
                                        <i class="fas fa-{{ $selectedCompetencia->estaVigente() ? 'check-circle' : 'times-circle' }} me-1"></i>
                                        {{ $selectedCompetencia->estaVigente() ? 'Vigente' : 'No vigente' }}
                                    </span>
                                </div>
                                <div class="status-description">
                                    @if($selectedCompetencia->tieneFechasDefinidas())
                                        Vigente desde {{ $selectedCompetencia->fechaInicioFormateada }} hasta {{ $selectedCompetencia->fechaFinFormateada }}
                                    @else
                                        Sin fechas de vigencia definidas
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección: Acciones -->
                        <div class="section-card section-actions">
                            <h6 class="section-title">Acciones</h6>
                            <div class="quick-actions">
                                @can('EDITAR COMPETENCIA')
                                    <button wire:click="openEditModal({{ $selectedCompetencia->id }})" 
                                            class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>
                                        Editar competencia
                                    </button>
                                @endcan
                                @can('GESTIONAR RESULTADOS COMPETENCIA')
                                    <a href="{{ route('competencias.gestionarResultados', $selectedCompetencia->id) }}" 
                                       class="btn btn-info">
                                        <i class="fas fa-tasks me-2"></i>
                                        Gestionar Resultados
                                    </a>
                                @endcan
                                <button wire:click="toggleStatus({{ $selectedCompetencia->id }})" 
                                        wire:loading.attr="disabled"
                                        class="btn {{ $selectedCompetencia->status ? 'btn-danger' : 'btn-success' }}">
                                    <span wire:loading.remove wire:target="toggleStatus">
                                        <i class="fas fa-sync-alt me-2"></i>
                                        {{ $selectedCompetencia->status ? 'Desactivar competencia' : 'Activar competencia' }}
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
                                        <div class="audit-user">{{ $selectedCompetencia->userCreate->name ?? 'Sistema' }}</div>
                                        <div class="audit-date">{{ $selectedCompetencia->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                                @if($selectedCompetencia->userEdit)
                                    <div class="audit-block">
                                        <div class="audit-label">Última modificación</div>
                                        <div class="audit-info">
                                            <div class="audit-user">{{ $selectedCompetencia->userEdit->name }}</div>
                                            <div class="audit-date">{{ $selectedCompetencia->updated_at->format('d/m/Y H:i') }}</div>
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
    @if ($showDeleteModal && $selectedCompetencia)
        <div class="modal-overlay" wire:click="$set('showDeleteModal', false)">
            <div class="modal-container modal-sm" wire:click.stop>
                
                <!-- Header Simple Unificado -->
                <div class="modal-header-simple">
                    <div>
                        <h4 class="modal-title text-danger">Eliminar competencia</h4>
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
                                <strong>¿Está seguro de que desea eliminar esta competencia?</strong>
                                <p class="mb-0 mt-1">
                                    Código: <strong>{{ $selectedCompetencia->codigo }}</strong><br>
                                    Nombre: <strong>{{ $selectedCompetencia->nombre }}</strong>
                                </p>
                            </div>
                        </div>
                        
                        @if($selectedCompetencia->programasFormacion->count() > 0)
                            <div class="alert alert-danger mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>No se puede eliminar.</strong> Esta competencia tiene {{ $selectedCompetencia->programasFormacion->count() }} programa(s) asociado(s).
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
                        @if($selectedCompetencia->programasFormacion->count() == 0)
                            <button wire:click="deleteCompetencia({{ $selectedCompetencia->id }})" 
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
