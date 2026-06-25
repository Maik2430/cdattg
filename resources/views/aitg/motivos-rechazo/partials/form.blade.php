@props(['action', 'method', 'motivo' => null])

<form action="{{ $action }}" method="POST" class="card card-body">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif
    <div class="row">
        <div class="col-md-3 form-group">
            <label>Código *</label>
            <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $motivo?->codigo) }}" required>
        </div>
        <div class="col-md-6 form-group">
            <label>Nombre *</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $motivo?->nombre) }}" required>
        </div>
        <div class="col-md-3 form-group">
            <label>Orden</label>
            <input type="number" name="orden" class="form-control" value="{{ old('orden', $motivo?->orden ?? 0) }}" min="0">
        </div>
    </div>
    <div class="form-group">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $motivo?->descripcion) }}</textarea>
    </div>
    <div class="custom-control custom-checkbox mb-3">
        <input type="checkbox" class="custom-control-input" id="activo" name="activo" value="1" @checked(old('activo', $motivo?->activo ?? true))>
        <label class="custom-control-label" for="activo">Activo</label>
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('aitg.motivos-rechazo.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
