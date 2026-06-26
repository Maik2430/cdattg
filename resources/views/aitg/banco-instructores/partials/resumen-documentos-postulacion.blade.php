@props(['postulacion', 'validacionService' => null])

@php
    $validacionService = $validacionService ?? app(\App\Services\Aitg\Banco\AitgBancoValidacionService::class);
    $archivos = $validacionService->archivosFaseDocumental($postulacion);
@endphp

@if($archivos->isNotEmpty())
    <div class="aitg-card aitg-card--info mb-4">
        <div class="aitg-card__header py-2">
            <h4 class="aitg-card__title mb-0"><i class="fas fa-clipboard-check"></i> Estado de sus documentos — {{ $postulacion->faseDocumentalLabel() }}</h4>
        </div>
        <div class="aitg-card__body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Documento</th>
                            <th>Decisión</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivos as $archivoPostulacion)
                            @php
                                $titulo = $archivoPostulacion->tipoArchivo?->nombre
                                    ?? ($archivoPostulacion->puntoAdicional?->descripcion ?? 'Documento');
                                $motivo = $validacionService->motivoRechazoActual($archivoPostulacion);
                            @endphp
                            <tr>
                                <td>{{ $titulo }}</td>
                                <td>
                                    <span class="badge badge-{{ match($archivoPostulacion->estado) {
                                        'aprobado' => 'success',
                                        'rechazado' => 'danger',
                                        'en_revision' => 'info',
                                        default => 'secondary'
                                    } }}">
                                        {{ ucfirst(str_replace('_', ' ', $archivoPostulacion->estado)) }}
                                    </span>
                                </td>
                                <td class="small">
                                    @if($motivo && $archivoPostulacion->estado === 'rechazado')
                                        <span class="text-danger">{{ $motivo }}</span>
                                    @elseif($archivoPostulacion->estado === 'aprobado')
                                        <span class="text-success">Documento aprobado</span>
                                    @elseif($archivoPostulacion->estado === 'en_revision')
                                        <span class="text-muted">En revisión por el validador</span>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
