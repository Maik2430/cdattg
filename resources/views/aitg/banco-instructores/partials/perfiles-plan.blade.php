@props(['plan', 'postulacion', 'etiquetaBloque', 'rutaPerfil' => null])

@if($postulacion->esConvocatoria())
    @php $rutaPerfil = $rutaPerfil ?? route('aitg.convocatorias.publicas.perfil', $postulacion->convocatoria_id); @endphp

    <div class="aitg-card aitg-card--success mb-4">
        <div class="aitg-card__header">
            <div class="aitg-card__title-wrap">
                <span class="aitg-card__icon aitg-card__icon--success"><i class="fas fa-layer-group"></i></span>
                <h3 class="aitg-card__title">Perfil de postulación (convocatoria)</h3>
            </div>
        </div>
        <div class="aitg-card__body">
            @if($plan->competencia)
                <div class="alert alert-light border mb-3">
                    <strong>Competencia:</strong> {{ $plan->competencia->nombre }}
                </div>
            @endif

            @if($postulacion->puedeEditar() && $postulacion->estado === 'borrador')
                <p class="aitg-card__hint">Seleccione el perfil (alternativa u opción) al que aplica en esta convocatoria:</p>
                <form action="{{ $rutaPerfil }}" method="POST">
                    @csrf
                    @foreach($plan->perfiles as $perfil)
                        <div class="custom-control custom-radio mb-2 p-2 border rounded aitg-perfil-radio">
                            <input type="radio" id="perfil_{{ $perfil->id }}" name="perfil_plan_id" value="{{ $perfil->id }}"
                                class="custom-control-input" @checked($postulacion->perfil_plan_id == $perfil->id) required>
                            <label class="custom-control-label w-100" for="perfil_{{ $perfil->id }}">
                                <strong>{{ $etiquetaBloque($perfil->consecutivo, $plan->perfiles->count()) }}:</strong>
                                {{ $perfil->descripcion_criterio }}
                            </label>
                        </div>
                    @endforeach
                    <button type="submit" class="btn btn-success btn-sm mt-2"><i class="fas fa-check"></i> Confirmar perfil seleccionado</button>
                </form>
            @elseif($postulacion->perfilPlan)
                <div class="alert alert-info mb-0">
                    <strong>Perfil seleccionado:</strong> {{ $postulacion->perfilPlan->descripcion_criterio }}
                </div>
            @elseif($postulacion->requierePerfil())
                <div class="alert alert-warning mb-0">
                    Aún no ha seleccionado el perfil al que aplica en esta convocatoria.
                </div>
            @endif
        </div>
    </div>
@endif
