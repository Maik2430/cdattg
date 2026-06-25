@props(['tipo', 'documento' => null, 'puedeEditar' => false])

<div class="card mb-3 border-left-{{ $documento ? match($documento->estado) {
    'aprobado' => 'success',
    'rechazado' => 'danger',
    'en_revision' => 'info',
    default => 'secondary'
} : 'secondary' }}">
    <div class="card-body py-3">
        <div class="row align-items-start">
            <div class="col-md-4">
                <h5 class="mb-1">{{ $tipo->nombre }}
                    @if($tipo->es_obligatorio)<span class="text-danger">*</span>@endif
                </h5>
                @if($tipo->descripcion)<small class="text-muted">{{ $tipo->descripcion }}</small>@endif
                <div class="mt-1"><small class="text-muted">Formatos: {{ implode(', ', $tipo->extensiones_permitidas ?? ['pdf']) }} · Máx. {{ $tipo->tamano_max_kb }} KB</small></div>
            </div>
            <div class="col-md-4">
                @if($documento)
                    <span class="badge badge-{{ match($documento->estado) {
                        'aprobado' => 'success',
                        'rechazado' => 'danger',
                        'en_revision' => 'info',
                        default => 'secondary'
                    } }}">{{ $documento->estado_label }}</span>
                    <div class="mt-2">
                        <a href="{{ route('aitg.banco-instructores.documentos.download', $documento) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> Ver documento
                        </a>
                        @if($puedeEditar)
                            <form action="{{ route('aitg.banco-instructores.documentos.destroy', $documento) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este documento?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        @endif
                    </div>
                    @php $ultima = $documento->ultimaValidacion(); @endphp
                    @if($ultima && $ultima->resultado === 'rechazado')
                        <div class="alert alert-warning mt-2 mb-0 py-2">
                            <strong>Motivo:</strong> {{ $ultima->motivoRechazo->nombre ?? '—' }}
                            @if($ultima->descripcion)<br><small>{{ $ultima->descripcion }}</small>@endif
                        </div>
                    @elseif($ultima && $ultima->resultado === 'aprobado')
                        <div class="text-success mt-2"><small><i class="fas fa-check"></i> Validado el {{ $ultima->fecha_validacion->format('d/m/Y') }}</small></div>
                    @endif
                @else
                    <span class="badge badge-light">Sin cargar</span>
                @endif
            </div>
            <div class="col-md-4">
                @if($puedeEditar)
                    <form action="{{ route('aitg.banco-instructores.documentos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tipo_archivo_id" value="{{ $tipo->id }}">
                        <div class="form-group mb-2">
                            <input type="file" name="archivo" class="form-control-file @error('archivo') is-invalid @enderror" required>
                            @error('archivo')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-upload"></i> {{ $documento ? 'Actualizar' : 'Cargar' }}
                        </button>
                    </form>
                @elseif(! $documento)
                    <span class="text-muted"><small>—</small></span>
                @endif
            </div>
        </div>
    </div>
</div>
