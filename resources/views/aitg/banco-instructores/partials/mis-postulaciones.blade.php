@props(['misPostulaciones'])

<div class="aitg-card aitg-card--success mt-4">
    <div class="aitg-card__header">
        <div class="aitg-card__title-wrap">
            <span class="aitg-card__icon aitg-card__icon--success"><i class="fas fa-list-alt"></i></span>
            <h3 class="aitg-card__title">Mis postulaciones</h3>
        </div>
    </div>
    <div class="aitg-card__body">
        <p class="text-muted small">Registros en el Banco de Talento por competencia. Al estar <strong>habilitado</strong> podrá postular en convocatorias abiertas.</p>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Competencia / Plan</th>
                        <th>Estado</th>
                        <th>Fase documental</th>
                        <th>Última actualización</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($misPostulaciones as $postulacion)
                        @php
                            $urlContinuar = $postulacion->convocatoria_id
                                ? route('aitg.convocatorias.publicas.postular', $postulacion->convocatoria_id)
                                : route('aitg.banco-instructores.postulacion', $postulacion->competencia_id ?? $postulacion->plan?->competencia_id);
                            $urlEliminar = $postulacion->convocatoria_id
                                ? route('aitg.convocatorias.publicas.postulacion.destroy', $postulacion->convocatoria_id)
                                : route('aitg.banco-instructores.postulacion.destroy', $postulacion->competencia_id ?? $postulacion->plan?->competencia_id);
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $postulacion->nombreCompetencia() }}</strong>
                                @if($postulacion->convocatoria)
                                    <br><small class="text-muted">Convocatoria: {{ \Illuminate\Support\Str::limit($postulacion->convocatoria->titulo, 50) }}</small>
                                @endif
                                @if($postulacion->perfilPlan)
                                    <br><small class="text-muted">Perfil: {{ \Illuminate\Support\Str::limit($postulacion->perfilPlan->descripcion_criterio, 60) }}</small>
                                @elseif($postulacion->esConvocatoria())
                                    <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Sin perfil seleccionado</small>
                                @endif
                                @if($postulacion->estado === 'requiere_correccion' && $postulacion->observaciones_validador)
                                    <br><small class="text-danger"><strong>Devuelta:</strong> {{ \Illuminate\Support\Str::limit($postulacion->observaciones_validador, 120) }}</small>
                                @endif
                                @php
                                    $rechazados = $postulacion->archivos->where('estado', 'rechazado');
                                @endphp
                                @if($rechazados->isNotEmpty())
                                    <br><small class="text-danger"><strong>{{ $rechazados->count() }} documento(s) rechazado(s)</strong> — use Corregir para actualizarlos.</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ match($postulacion->estado) {
                                    'aprobado' => 'success',
                                    'rechazado', 'requiere_correccion' => 'warning',
                                    'pendiente_revision' => 'info',
                                    'preseleccionado' => 'primary',
                                    default => 'secondary',
                                } }}">{{ $postulacion->estado_label }}</span>
                                @if($postulacion->estado === 'aprobado')
                                    <br><small class="text-success">Puede postular en convocatorias de esta competencia.</small>
                                @endif
                            </td>
                            <td><small>{{ $postulacion->faseDocumentalLabel() }}</small></td>
                            <td><small>{{ $postulacion->updated_at->format('d/m/Y H:i') }}</small></td>
                            <td class="text-center">
                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                    @if($postulacion->puedeEditar())
                                        <a href="{{ $urlContinuar }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                            @if($postulacion->estado === 'requiere_correccion')
                                                Corregir documentos
                                            @else
                                                Continuar
                                            @endif
                                        </a>
                                    @else
                                        <a href="{{ $urlContinuar }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Ver estado</a>
                                    @endif
                                    @if($postulacion->puedeEliminar())
                                        <form action="{{ $urlEliminar }}" method="POST" class="d-inline" onsubmit="return confirm(@json($postulacion->mensajeConfirmacionEliminar()))">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar postulación">
                                                <i class="fas fa-trash"></i>
                                                @if($postulacion->estado === 'pendiente_revision')
                                                    Retirar
                                                @else
                                                    Eliminar
                                                @endif
                                            </button>
                                        </form>
                                    @elseif($postulacion->esBancoTalento() && ($msg = $postulacion->mensajeNoEliminable()))
                                        <span class="text-muted small" title="{{ $msg }}"><i class="fas fa-lock"></i></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Aún no se ha inscrito en ninguna competencia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
