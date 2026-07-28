<div>
    <div class="gestionar-competencias">
        @if($resultado)
            <h3>{{ $resultado->codigo }} · {{ $resultado->nombre }}</h3>
            <p>Asignadas: {{ is_countable($asignados) ? count($asignados) : 0 }}</p>
            <p>Disponibles: {{ is_countable($disponibles) ? count($disponibles) : 0 }}</p>
        @else
            <p>Resultado de aprendizaje no encontrado.</p>
        @endif
    </div>
</div>
