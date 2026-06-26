@props(['postulacion', 'rutaCancelar', 'etiqueta' => null])

@php
    $etiqueta = $etiqueta ?? match ($postulacion->estado) {
        'pendiente_revision' => 'Retirar postulación en revisión',
        'requiere_correccion' => 'Eliminar y empezar de nuevo',
        default => 'Eliminar postulación',
    };
@endphp

@if($postulacion->puedeEliminar() && $rutaCancelar)
    <form
        action="{{ $rutaCancelar }}"
        method="POST"
        class="text-right mb-4"
        onsubmit="return confirm(@json($postulacion->mensajeConfirmacionEliminar()))"
    >
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger">
            <i class="fas fa-trash"></i> {{ $etiqueta }}
        </button>
    </form>
@elseif($postulacion->esBancoTalento() && ($mensaje = $postulacion->mensajeNoEliminable()))
    <p class="text-muted small text-right mb-4"><i class="fas fa-info-circle"></i> {{ $mensaje }}</p>
@endif
