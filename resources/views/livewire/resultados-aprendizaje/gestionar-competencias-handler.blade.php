<div>
    <!-- Header mejorado - Guía de acción clara -->
    <div class="page-header-card">
        <div class="header-left">
            <div class="header-icon">
                <i class="fas fa-link"></i>
            </div>
            <div class="header-text">
                <h2>Gestionar Competencias</h2>
                <div class="header-subtitle">
                    <strong>{{ $resultado->codigo }}</strong> · {{ $resultado->nombre }}
                </div>
                <div class="header-meta">
                    <span>{{ $competenciasAsignadas->count() }} asignadas</span>
                    <span class="dot">•</span>
                    <span>{{ $competenciasDisponibles->count() }} disponibles</span>
                </div>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('resultados-aprendizaje.index') }}" class="btn btn-outline-secondary">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Layout principal - DOS COLUMNAS SIMPLES -->
    <div class="row">
        <!-- Competencias Asignadas -->
        <div class="col-md-6">
            <div class="competencias-panel">
                <div class="competencias-header">
                    <span>
                        <i class="fas fa-check-circle text-success mr-1"></i>
                        Competencias asignadas ({{ $competenciasAsignadas->count() }})
                    </span>
                </div>
                <div class="competencias-body scrollable">
                    @if($competenciasAsignadas->isEmpty())
                        <div class="empty-state">
                            <p>No hay competencias asignadas.</p>
                            <small>Use la lista de la derecha para agregar nuevas.</small>
                        </div>
                    @else
                        @foreach($competenciasAsignadas as $competencia)
                            <div class="competencia-item">
                                <span class="competencia-code">{{ $competencia->codigo }}</span>
                                <span class="competencia-name">{{ $competencia->nombre }}</span>
                                <button onclick="confirmarDesasociar({{ $competencia->id }}, '{{ $competencia->codigo }} - {{ $competencia->nombre }}')" 
                                        class="btn-action btn-remove"
                                        title="Desasociar competencia">
                                    <i class="fas fa-minus"></i> Quitar
                                </button>
                            </div>
                        @endforeach
                        
                        <!-- Empty hint elegante cuando hay pocas asignadas -->
                        @if($competenciasAsignadas->count() <= 1)
                            <div class="empty-hint">
                                <small>
                                    Puedes agregar más competencias desde la lista de la derecha.
                                </small>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Competencias Disponibles -->
        <div class="col-md-6">
            <div class="competencias-panel">
                <div class="competencias-header">
                    <span>
                        <i class="fas fa-plus-circle text-primary mr-1"></i>
                        Competencias disponibles ({{ $competenciasDisponibles->count() }})
                    </span>
                </div>
                <div class="competencias-body scrollable">
                    @if($competenciasDisponibles->isEmpty())
                        <div class="empty-state">
                            <p>No hay más competencias disponibles.</p>
                            <small>Todas las competencias están asignadas.</small>
                        </div>
                    @else
                        @foreach($competenciasDisponibles as $competencia)
                            <div class="competencia-item">
                                <span class="competencia-code">{{ $competencia->codigo }}</span>
                                <span class="competencia-name">{{ $competencia->nombre }}</span>
                                <button onclick="confirmarAsociar({{ $competencia->id }}, '{{ $competencia->codigo }} - {{ $competencia->nombre }}')" 
                                        class="btn-action btn-add"
                                        title="Asignar competencia">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
