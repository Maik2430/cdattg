@php
    /** @var \App\Models\Persona $persona */
    $isActive = (int) $persona->status === 1;
@endphp

<span class="badge badge-{{ $isActive ? 'success' : 'danger' }}">
    {{ $isActive ? 'ACTIVO' : 'INACTIVO' }}
</span>

