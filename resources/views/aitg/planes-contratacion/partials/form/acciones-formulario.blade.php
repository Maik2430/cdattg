@props(['submitLabel' => 'Guardar plan'])

{{-- Acciones finales del formulario --}}
<div class="aitg-form-actions">
    <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-save mr-1"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('aitg.planes-contratacion.index') }}" class="btn btn-outline-secondary btn-lg">Cancelar</a>
</div>
