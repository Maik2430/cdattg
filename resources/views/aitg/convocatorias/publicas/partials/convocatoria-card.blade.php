@props(['conv', 'postulacionUsuario' => null, 'puedePostular' => false, 'mensajeBloqueo' => null])

<div class="card h-100 shadow-sm aitg-convocatoria-card border-left-{{ $conv->badgeClassEstado() }}">
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="badge badge-{{ $conv->badgeClassEstado() }}">{{ $conv->estado_label }}</span>
            <small class="text-muted">{{ $conv->codigo }}</small>
        </div>
        <h5 class="card-title">{{ $conv->titulo }}</h5>
        <p class="text-muted small mb-2">{{ $conv->competencia->nombre ?? '—' }}</p>
        <ul class="list-unstyled small text-muted mb-3 flex-grow-1">
            <li><i class="fas fa-map-marker-alt text-secondary"></i> {{ $conv->regional->nombre ?? '—' }}</li>
            <li><i class="fas fa-building text-secondary"></i> {{ $conv->centroFormacion->nombre ?? '—' }}</li>
            <li><i class="fas fa-calendar-alt text-secondary"></i>
                {{ $conv->fecha_inicio_publicacion?->format('d/m/Y') ?? '—' }}
                – {{ $conv->fecha_fin_publicacion?->format('d/m/Y') ?? '—' }}
            </li>
        </ul>

        @if($conv->estado === 'finalizada' && $conv->postulacionSeleccionada?->user?->persona)
            <div class="alert alert-info py-2 small mb-3">
                <strong>Seleccionado:</strong>
                {{ $conv->postulacionSeleccionada->user->persona->nombre_completo ?? '—' }}
            </div>
        @endif

        @if($postulacionUsuario)
            <div class="alert alert-light border py-2 small mb-3">
                <strong>Mi postulación:</strong> {{ $postulacionUsuario->estado_label }}
                @if($postulacionUsuario->perfilPlan)
                    <br><span class="text-muted">{{ \Illuminate\Support\Str::limit($postulacionUsuario->perfilPlan->descripcion_criterio, 60) }}</span>
                @endif
            </div>
        @elseif($mensajeBloqueo)
            <div class="alert alert-warning py-2 small mb-3">{{ $mensajeBloqueo }}</div>
        @endif

        <div class="d-flex flex-wrap gap-1 mt-auto">
            <a href="{{ route('aitg.convocatorias.publicas.show', $conv) }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-eye"></i> Ver
            </a>
            @if($postulacionUsuario?->puedeEditar())
                <a href="{{ route('aitg.convocatorias.publicas.postular', $conv) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Continuar
                </a>
            @elseif($puedePostular && ! $postulacionUsuario)
                <a href="{{ route('aitg.convocatorias.publicas.postular', $conv) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-paper-plane"></i> Postularme
                </a>
            @endif
            @if($postulacionUsuario?->puedeEliminar())
                <form action="{{ route('aitg.convocatorias.publicas.postulacion.destroy', $conv) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar su postulación a esta convocatoria?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar postulación"><i class="fas fa-trash"></i></button>
                </form>
            @endif
        </div>
    </div>
</div>
