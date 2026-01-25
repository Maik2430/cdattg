<div>
    <!-- Toast Minimalista ERP -->
    <div class="toast toast-minimal">
        <i class="toast-icon"></i>
        <span class="toast-text"></span>
    </div>

    <!-- Header de la Competencia - Nivel 1: Identidad -->
    <div class="competencia-identity mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="d-flex align-items-center mb-2">
                    <h3 class="competencia-title mb-0">
                        {{ $competencia->nombre }}
                    </h3>
                    <span class="badge-modern badge-status badge-status-small {{ $competencia->status ? 'badge-success' : 'badge-danger' }} ml-3">
                        {{ $competencia->status ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
                <div class="competencia-meta">
                    <span class="badge-modern badge-primary">{{ $competencia->codigo }}</span>
                </div>
                <p class="competencia-descripcion">
                    {{ $competencia->descripcion }}
                </p>
            </div>
            <div>
                <a href="{{ route('competencias.index') }}" class="btn-link-back">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver a Competencias
                </a>
            </div>
        </div>
    </div>

    <!-- Métricas - Nivel 2: Resumen Real -->
    <div class="metrics-section mb-4">
        <div class="metrics-header">
            <h5 class="metrics-title">Resumen de Resultados</h5>
        </div>
        <div class="metrics-grid-compact">
            <div class="metric-card-compact">
                <div class="metric-value-small">{{ $totalAsignados }}</div>
                <div class="metric-label-small">asignados</div>
            </div>
            <div class="metric-card-compact">
                <div class="metric-value-small">{{ $totalDisponibles }}</div>
                <div class="metric-label-small">disponibles</div>
            </div>
            <div class="metric-card-compact">
                <div class="metric-value-small">{{ $this->formatearHoras($duracionTotal) }} h</div>
                <div class="metric-label-small">horas</div>
            </div>
            <div class="metric-status-compact">
                <div class="status-indicator {{ $competencia->status ? 'status-active' : 'status-inactive' }}"></div>
                <div class="status-text-small">{{ $competencia->status ? 'Activa' : 'Inactiva' }}</div>
            </div>
        </div>
        
        <!-- Mensaje Contextual -->
        @if($totalAsignados === 0)
            <div class="contextual-message-compact">
                <div class="message-icon-small">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="message-content-small">
                    <span class="text-muted">Aún no hay resultados asociados. Asigne resultados de aprendizaje para continuar.</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Resultados de Aprendizaje - Flujo Unificado -->
    <div class="results-container">
        @if($resultadosAsignados->isEmpty())
            <!-- Estado Vacío + Acción Principal -->
            <div class="results-empty-state">
                <div class="empty-icon">
                    <i class="fas fa-list-check"></i>
                </div>
                <h4>No hay resultados asociados</h4>
                <p>
                    Esta competencia aún no tiene resultados de aprendizaje.
                    Puede asociarlos para continuar con la configuración.
                </p>
                @can('GESTIONAR RESULTADOS COMPETENCIA')
                    <button wire:click="openAsociarModal" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        Asociar resultados de aprendizaje
                    </button>
                @endcan
            </div>
        @else
            <!-- Estado con Datos - Tabla Principal -->
            <div class="card shadow-sm no-hover">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-check-circle"></i> Resultados de Aprendizaje Asignados
                        </h6>
                        @can('GESTIONAR RESULTADOS COMPETENCIA')
                            <button wire:click="openAsociarModal" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Asociar más
                            </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <!-- Buscador -->
                    <div class="form-group mb-3">
                        <div class="input-group">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="searchAsignados"
                                   class="form-control" 
                                   placeholder="Buscar en resultados asignados...">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Duración</th>
                                    <th width="80" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resultadosAsignados as $resultado)
                                    <tr>
                                        <td>
                                            <span class="badge-modern badge-info">{{ $resultado->codigo }}</span>
                                        </td>
                                        <td>{{ $resultado->nombre }}</td>
                                        <td>{{ $this->formatearHoras($resultado->pivot->duracion ?? 0) }} hrs</td>
                                        <td class="text-center">
                                            @can('GESTIONAR RESULTADOS COMPETENCIA')
                                                <button wire:click="openDesasociarModal({{ $resultado->id }})" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Desasociar">
                                                    <i class="fas fa-unlink"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Información y Paginación -->
                    <div class="table-footer">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Total: <strong>{{ $resultadosAsignados->total() }}</strong> resultado(s) | 
                            Duración total: <strong>{{ $this->formatearHoras($duracionTotal) }}</strong> horas
                        </small>
                        
                        <!-- Paginación -->
                        <div class="pagination-wrapper">
                            <div class="pagination-modern">
                                <div class="pagination-info">
                                    Mostrando {{ $resultadosAsignados->firstItem() ?? 0 }} a {{ $resultadosAsignados->lastItem() ?? 0 }} 
                                    de {{ $resultadosAsignados->total() }} resultados
                                </div>
                                <div class="pagination-links">
                                    {{ $resultadosAsignados->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Resultados Disponibles - Solo se muestran si hay datos asignados -->
        @if(!$resultadosAsignados->isEmpty() && $resultadosDisponibles->isNotEmpty())
            <div class="available-results-section mt-4">
                <div class="card shadow-sm no-hover">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-plus-circle"></i> Resultados Disponibles para Asociar
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Mensaje de acción -->
                        <div class="action-message mb-3">
                            <div class="action-icon">
                                <i class="fas fa-hand-pointer"></i>
                            </div>
                            <div class="action-content">
                                <strong>Seleccione resultados disponibles para asociarlos.</strong>
                            </div>
                        </div>

                        <!-- Buscador -->
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <input type="text" 
                                       wire:model.live.debounce.300ms="searchDisponibles"
                                       class="form-control" 
                                       placeholder="Buscar disponibles...">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla -->
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th width="80" class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resultadosDisponibles as $resultado)
                                        <tr>
                                            <td>
                                                <span class="badge-modern badge-secondary">{{ $resultado->codigo }}</span>
                                            </td>
                                            <td>{{ $resultado->nombre }}</td>
                                            <td class="text-center">
                                                <button wire:click="asociarResultadoDirecto({{ $resultado->id }})" 
                                                        class="btn btn-sm btn-success" 
                                                        title="Asociar">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Total: <strong>{{ $resultadosDisponibles->count() }}</strong> resultado(s) disponibles
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Asociar Resultados -->
    @if ($showAsociarModal)
        <div class="modal-overlay" wire:click="$set('showAsociarModal', false)">
            <div class="modal-container modal-sm" wire:click.stop>
                <div class="modal-header-simple">
                    <div>
                        <h4 class="modal-title">Asociar Resultados de Aprendizaje</h4>
                        <p class="modal-subtitle">
                            Competencia: {{ $competencia->codigo }} - {{ Str::limit($competencia->nombre, 50) }}
                        </p>
                    </div>
                    <button class="modal-close" wire:click="$set('showAsociarModal', false)">✕</button>
                </div>

                <div class="modal-body">
                    <div class="modal-content-wrapper">
                        <!-- Header de la sección -->
                        <div class="section-header">
                            <h6 class="section-title">Resultados de Aprendizaje disponibles</h6>
                            <p class="section-subtitle">Seleccione uno o varios resultados para asociarlos</p>
                        </div>

                        <!-- Lista de resultados con checkboxes -->
                        <div class="results-list">
                            @foreach($resultadosDisponibles as $resultado)
                                <label class="list-item">
                                    <input 
                                        type="checkbox" 
                                        wire:model="selectedResultados" 
                                        value="{{ $resultado->id }}"
                                        class="list-checkbox"
                                    >
                                    <div class="list-content">
                                        <div class="list-code">{{ $resultado->codigo }}</div>
                                        <div class="list-name">{{ $resultado->nombre }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <!-- Feedback de selección -->
                        <div class="selection-info">
                            {{ count($selectedResultados) }} resultado(s) seleccionado(s)
                        </div>

                        <div class="form-group">
                            <button 
                                wire:click="asociarResultados" 
                                class="btn btn-success btn-block"
                                @disabled(count($selectedResultados) === 0)
                            >
                                <i class="fas fa-link me-2"></i>
                                Asociar resultados seleccionados
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Confirmación Desasociar -->
    @if ($showDesasociarModal && $selectedResultado)
        <div class="modal-overlay" wire:click="$set('showDesasociarModal', false)">
            <div class="modal-container modal-sm" wire:click.stop>
                <div class="modal-header-simple">
                    <div>
                        <h4 class="modal-title text-danger">Desasociar Resultado</h4>
                        <p class="modal-subtitle">Esta acción no se puede deshacer</p>
                    </div>
                    <button class="modal-close" wire:click="$set('showDesasociarModal', false)">✕</button>
                </div>

                <div class="modal-body">
                    <div class="modal-content-wrapper">
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>¿Está seguro de que desea desasociar este resultado?</strong>
                                <p class="mb-0 mt-1">
                                    Código: <strong>{{ $selectedResultado->codigo }}</strong><br>
                                    Nombre: <strong>{{ $selectedResultado->nombre }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer-erp">
                    <div class="footer-actions">
                        <button wire:click="$set('showDesasociarModal', false)" class="btn-erp btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </button>
                        <button wire:click="desasociarResultado({{ $selectedResultado->id }})" class="btn-erp btn-danger">
                            <i class="fas fa-unlink"></i>
                            Desasociar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
