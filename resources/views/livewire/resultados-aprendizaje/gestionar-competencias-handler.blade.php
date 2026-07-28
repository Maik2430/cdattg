<div class="gestionar-competencias-handler">
    @if($resultado)
        <h3>{{ $resultado->codigo }} · {{ $resultado->nombre }}</h3>
        <p>Asignadas: {{ $competenciasAsignadas?->count() ?? 0 }}</p>
        <p>Disponibles: {{ $competenciasDisponibles?->count() ?? 0 }}</p>
    @else
        <p>Resultado de aprendizaje no encontrado.</p>
    @endif
</div>
