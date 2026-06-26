@props(['secciones', 'plan' => null, 'competencia' => null, 'postulacion', 'rutaDocumento' => null, 'rutaReutilizar' => null, 'rutaEliminar' => null, 'rutaCancelarPostulacion' => null])

@php
    $contexto = $competencia ?? $plan;
    $rutaDocumento = $rutaDocumento ?? route('aitg.banco-instructores.documentos.store', $contexto);
    $rutaReutilizar = $rutaReutilizar ?? route('aitg.banco-instructores.reutilizar', $contexto);
    $rutaEliminar = $rutaEliminar ?? route('aitg.banco-instructores.documentos.destroy', ['competencia' => $competencia?->id ?? $plan?->id, 'postulacionArchivo' => '__ID__']);
    if ($plan && ! $competencia) {
        $rutaEliminar = route('aitg.banco-instructores.documentos.destroy', ['competencia' => $plan->competencia_id, 'postulacionArchivo' => '__ID__']);
    }
    $modoSubsanacion = $postulacion->estado === 'requiere_correccion';
    $tieneItemsAccion = collect($secciones)->flatMap(fn ($s) => $s['items'])->contains(fn ($i) => $i['requiere_accion'] ?? true);
    $maxMbPorArchivo = 10;
    $exigePerfil = $postulacion->esConvocatoria() && $postulacion->requierePerfil();
@endphp

@if($modoSubsanacion)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Subsanación documental.</strong>
        Revise las observaciones del validador y corrija <strong>solo los documentos rechazados</strong>.
        Los documentos marcados como <span class="badge badge-success">Aprobado</span> no deben modificarse.
        @if($plan->competencia)
            Postulación para la competencia: <strong>{{ $plan->competencia->nombre }}</strong>.
        @endif
    </div>
@endif

@foreach($secciones as $seccion)
    <div class="aitg-card aitg-card--info mb-4 aitg-banco-seccion" data-seccion="{{ $seccion['key'] }}">
        <div class="aitg-card__header py-2">
            <h4 class="aitg-card__title mb-0">{{ $loop->iteration }}. {{ $seccion['titulo'] }}</h4>
        </div>
        <div class="aitg-card__body">
            @foreach($seccion['items'] as $item)
                @php
                    $vinculado = $item['vinculado'] ?? null;
                    $archivo = $vinculado?->archivoTalento;
                    $estadoDoc = $vinculado?->estado;
                    $requiereAccion = $item['requiere_accion'] ?? true;
                    $puedeCargar = $postulacion->puedeEditar() && $requiereAccion && (! $exigePerfil || $postulacion->perfil_plan_id);
                    $docAprobado = $modoSubsanacion && $estadoDoc === 'aprobado';
                    $puedeSubir = $puedeCargar && (
                        ! empty($item['tipo_archivo_id'])
                        || ! empty($item['checklist_item_id'])
                        || ! empty($item['punto_item_id'])
                        || ! empty($item['punto_adicional_id'])
                        || ! empty($item['perfil_plan_id'])
                    );
                @endphp
                <div class="aitg-doc-upload mb-4 pb-3 border-bottom {{ $docAprobado ? 'bg-light rounded px-2' : '' }}">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div class="mb-2">
                            <h5 class="mb-1">
                                {{ $item['nombre'] }}
                                @if($item['obligatorio'])<span class="text-danger">*</span>@endif
                                @if($item['permite_multiples'])<span class="badge badge-secondary">Varios PDF</span>@endif
                                @if($estadoDoc === 'rechazado')<span class="badge badge-danger ml-1">Rechazado — debe corregir</span>@endif
                                @if($docAprobado)<span class="badge badge-success ml-1">Aprobado — no requiere cambios</span>@endif
                            </h5>
                            <p class="text-muted small mb-0">{{ $item['descripcion'] }}</p>
                            @if(! empty($item['puntaje']))
                                <p class="text-muted small mb-0 mt-1">
                                    @if(($item['tipo'] ?? null) === 'checklist')
                                        <strong>Peso en evaluación:</strong> {{ number_format($item['peso_porcentual'] ?? $item['puntaje'], 2) }}% (peso igual entre ítems)
                                    @elseif(($item['tipo'] ?? null) === 'punto_adicional')
                                        <strong>Bonus opcional:</strong> +{{ number_format($item['puntaje'], 2) }} pts si cumple
                                    @endif
                                </p>
                            @endif
                            @if(! empty($item['motivo_rechazo']))
                                <p class="text-danger small mb-0 mt-1"><strong>Motivo del rechazo:</strong> {{ $item['motivo_rechazo'] }}</p>
                            @endif
                        </div>
                        @if($archivo)
                            <span class="badge badge-{{ match($estadoDoc) { 'aprobado' => 'success', 'rechazado' => 'danger', 'en_revision' => 'info', default => 'secondary' } }}">
                                <i class="fas fa-{{ $estadoDoc === 'rechazado' ? 'times' : ($estadoDoc === 'aprobado' ? 'check' : 'file') }}"></i>
                                {{ ucfirst(str_replace('_', ' ', $estadoDoc ?? 'cargado')) }}
                            </span>
                        @endif
                    </div>

                    @if($archivo)
                        <div class="bg-light rounded p-2 mt-2 d-flex flex-wrap align-items-center">
                            <span class="mr-2">
                                <i class="fas fa-file-pdf text-danger"></i>
                                {{ $archivo->nombre_original }}
                                <small class="text-muted">· {{ $archivo->created_at->format('d/m/Y H:i') }}</small>
                            </span>
                            <a href="{{ route('aitg.banco-instructores.archivos.ver', $archivo) }}" class="btn btn-xs btn-outline-info ml-1" target="_blank">Ver</a>
                            <a href="{{ route('aitg.banco-instructores.archivos.download', $archivo) }}" class="btn btn-xs btn-outline-primary ml-1">Descargar</a>
                            @if($puedeCargar && $vinculado && ! $docAprobado)
                                <form action="{{ str_replace('__ID__', $vinculado->id, $rutaEliminar) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('¿Eliminar este documento de su postulación?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger"><i class="fas fa-trash"></i> Eliminar</button>
                                </form>
                            @endif
                        </div>
                    @endif

                    @if($puedeCargar)
                        @if($item['boveda_disponible']->isNotEmpty() && ! $archivo)
                            @foreach($item['boveda_disponible'] as $existente)
                                <form action="{{ $rutaReutilizar }}" method="POST" class="d-inline mt-2">
                                    @csrf
                                    <input type="hidden" name="archivo_talento_id" value="{{ $existente->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-recycle"></i> Usar existente: {{ \Illuminate\Support\Str::limit($existente->nombre_original, 40) }}
                                    </button>
                                </form>
                            @endforeach
                        @endif

                        @if($puedeSubir)
                            <form action="{{ $rutaDocumento }}" method="POST" enctype="multipart/form-data" class="mt-2 aitg-upload-single" style="max-width: 520px;">
                                @csrf
                                @if(! empty($item['perfil_plan_id']))
                                    <input type="hidden" name="perfil_plan_id" value="{{ $item['perfil_plan_id'] }}">
                                @endif
                                @if(! empty($item['tipo_archivo_id']))
                                    <input type="hidden" name="tipo_archivo_id" value="{{ $item['tipo_archivo_id'] }}">
                                @endif
                                @if(! empty($item['checklist_item_id']))
                                    <input type="hidden" name="checklist_item_id" value="{{ $item['checklist_item_id'] }}">
                                @endif
                                @if(! empty($item['punto_item_id']))
                                    <input type="hidden" name="punto_item_id" value="{{ $item['punto_item_id'] }}">
                                @endif
                                @if(! empty($item['punto_adicional_id']))
                                    <input type="hidden" name="punto_adicional_id" value="{{ $item['punto_adicional_id'] }}">
                                @endif
                                <div class="custom-file">
                                    <input
                                        type="file"
                                        name="archivo"
                                        class="custom-file-input aitg-single-file"
                                        accept=".pdf,application/pdf"
                                        data-label="{{ $archivo ? 'Reemplazar PDF...' : 'Seleccionar PDF...' }}"
                                    >
                                    <label class="custom-file-label">{{ $archivo ? 'Reemplazar PDF...' : 'Seleccionar PDF...' }}</label>
                                </div>
                                <p class="text-muted small mb-0 mt-1">
                                    PDF máx. {{ $maxMbPorArchivo }} MB · se sube al seleccionarlo.
                                </p>
                            </form>
                        @endif
                    @elseif($docAprobado)
                        <p class="text-success small mt-2 mb-0"><i class="fas fa-check-circle"></i> Este documento fue aprobado por el validador.</p>
                    @elseif($exigePerfil && ! $postulacion->perfil_plan_id)
                        <p class="text-muted small mt-2 mb-0"><i class="fas fa-info-circle"></i> Seleccione un perfil para habilitar la carga.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endforeach

@if($postulacion->perfil_plan_id && empty($secciones))
    @if($modoSubsanacion)
        <p class="text-muted">No hay documentos pendientes de corrección. Puede reenviar su postulación.</p>
    @else
        <p class="text-muted">No hay documentos configurados para esta fase.</p>
    @endif
@endif

@once
    @push('js')
    <script>
    document.querySelectorAll('.aitg-single-file').forEach(function (input) {
        input.addEventListener('change', function () {
            const form = this.closest('form');
            const label = this.closest('.custom-file')?.querySelector('.custom-file-label');
            const defaultLabel = this.dataset.label || 'Seleccionar PDF...';

            if (! this.files || ! this.files.length) {
                if (label) label.textContent = defaultLabel;
                return;
            }

            if (this.files[0].size > {{ $maxMbPorArchivo }} * 1024 * 1024) {
                alert('El PDF supera {{ $maxMbPorArchivo }} MB. Comprima el archivo o use uno más liviano.');
                this.value = '';
                if (label) label.textContent = defaultLabel;
                return;
            }

            if (label) label.textContent = this.files[0].name + ' — subiendo…';

            if (form) {
                form.classList.add('opacity-75');
                form.submit();
            }
        });
    });
    </script>
    @endpush
@endonce
