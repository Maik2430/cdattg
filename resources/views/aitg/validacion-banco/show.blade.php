@extends('adminlte::page')

@section('title', 'Revisar solicitud - AITG')

@section('css')
    <x-vite-stylesheet paths="resources/css/aitg/planes-contratacion/app.css" />
@endsection

@section('content_header')
    @include('aitg.planes-contratacion.partials.layout.page-header', [
        'title' => 'Revisar solicitud #' . $solicitud->id,
        'subtitle' => trim(($solicitud->user->persona->primer_nombre ?? '') . ' ' . ($solicitud->user->persona->primer_apellido ?? '')) ?: $solicitud->user->email,
        'breadcrumb' => [
            ['label' => 'Validación', 'url' => route('aitg.validacion-banco.index'), 'icon' => 'fa-check-double'],
            ['label' => 'Detalle', 'active' => true],
        ],
    ])
@endsection

@section('content')
<section class="content aitg-content mt-2">
    <div class="container-fluid">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <div class="mb-3">
            <a href="{{ route('aitg.validacion-banco.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
            <span class="badge badge-info ml-2">{{ $solicitud->estado_label }}</span>
        </div>

        @foreach($solicitud->documentos as $documento)
            <div class="aitg-card aitg-card--success mb-3">
                <div class="aitg-card__header py-2">
                    <h4 class="aitg-card__title mb-0">{{ $documento->tipoArchivo->nombre }}
                        <span class="badge badge-{{ $documento->estado === 'aprobado' ? 'success' : ($documento->estado === 'rechazado' ? 'danger' : 'secondary') }} ml-2">{{ $documento->estado_label }}</span>
                    </h4>
                </div>
                <div class="aitg-card__body">
                    <p><strong>Archivo:</strong> {{ $documento->nombre_original }}
                        <a href="{{ route('aitg.banco-instructores.documentos.download', $documento) }}" class="btn btn-sm btn-outline-primary ml-2"><i class="fas fa-download"></i> Descargar</a>
                    </p>

                    @can('VALIDAR DOCUMENTO BANCO AITG')
                        @if(in_array($documento->estado, ['en_revision', 'pendiente', 'rechazado']))
                            <form action="{{ route('aitg.validacion-banco.documentos.validar', $documento) }}" method="POST" class="aitg-validacion-form border-top pt-3 mt-2">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label>Decisión <span class="text-danger">*</span></label>
                                        <select name="resultado" class="form-control aitg-toggle-rechazo" required>
                                            <option value="">Seleccione...</option>
                                            <option value="aprobado">Aprobar</option>
                                            <option value="rechazado">Rechazar</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group aitg-campo-motivo" style="display:none">
                                        <label>Motivo de rechazo <span class="text-danger">*</span></label>
                                        <select name="motivo_rechazo_id" class="form-control">
                                            <option value="">Seleccione motivo...</option>
                                            @foreach($motivosRechazo as $motivo)
                                                <option value="{{ $motivo->id }}">{{ $motivo->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5 form-group">
                                        <label>Descripción (opcional)</label>
                                        <textarea name="descripcion" rows="2" class="form-control" placeholder="Indique detalles adicionales..."></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-gavel"></i> Registrar validación</button>
                            </form>
                        @endif
                    @endcan

                    @if($documento->validaciones->isNotEmpty())
                        <div class="mt-3">
                            <strong>Historial:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($documento->validaciones as $v)
                                    <li>
                                        {{ $v->fecha_validacion->format('d/m/Y H:i') }} —
                                        <strong>{{ ucfirst($v->resultado) }}</strong>
                                        @if($v->motivoRechazo) ({{ $v->motivoRechazo->nombre }}) @endif
                                        @if($v->descripcion) — {{ $v->descripcion }} @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection

@section('js')
<script>
document.querySelectorAll('.aitg-toggle-rechazo').forEach(function (select) {
    const toggle = () => {
        const wrap = select.closest('.aitg-validacion-form');
        const motivo = wrap?.querySelector('.aitg-campo-motivo');
        if (motivo) motivo.style.display = select.value === 'rechazado' ? 'block' : 'none';
    };
    select.addEventListener('change', toggle);
    toggle();
});
</script>
@endsection
