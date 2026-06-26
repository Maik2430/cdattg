@props(['action', 'method', 'tipo' => null])

<form action="{{ $action }}" method="POST" class="card card-body">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    <div class="row">
        <div class="col-md-3 form-group">
            <label>Código *</label>
            <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $tipo?->codigo) }}" required>
        </div>
        <div class="col-md-5 form-group">
            <label>Nombre *</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $tipo?->nombre) }}" required>
        </div>
        <div class="col-md-2 form-group">
            <label>Orden</label>
            <input type="number" name="orden" class="form-control" value="{{ old('orden', $tipo?->orden ?? 0) }}" min="0">
        </div>
        <div class="col-md-2 form-group">
            <label>Tamaño máx. (KB) *</label>
            <input type="number" name="tamano_max_kb" class="form-control" value="{{ old('tamano_max_kb', $tipo?->tamano_max_kb ?? 5120) }}" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 form-group">
            <label>Fase de carga *</label>
            <select name="fase_carga" class="form-control" required>
                @foreach(\App\Models\Aitg\Banco\TipoArchivo::FASES_CARGA as $val => $label)
                    <option value="{{ $val }}" @selected(old('fase_carga', $tipo?->fase_carga ?? 'inicial') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <small class="text-muted">Postulación = antes de preselección. Formalización = tras ser preseleccionado.</small>
        </div>
        <div class="col-md-4 form-group">
            <label>Categoría *</label>
            <select name="categoria" class="form-control" required>
                @foreach(\App\Models\Aitg\Banco\TipoArchivo::CATEGORIAS as $val => $label)
                    @if($val !== 'puntos_adicionales')
                        <option value="{{ $val }}" @selected(old('categoria', $tipo?->categoria ?? 'obligatorios_base') === $val)>{{ $label }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label>Regla de visibilidad</label>
            <select name="regla_visibilidad" class="form-control">
                @foreach(\App\Models\Aitg\Banco\TipoArchivo::REGLAS_VISIBILIDAD as $val => $label)
                    <option value="{{ $val }}" @selected(old('regla_visibilidad', $tipo?->regla_visibilidad ?? 'siempre') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $tipo?->descripcion) }}</textarea>
    </div>

    <div class="row">
        <div class="col-md-4 form-group">
            <label>Extensiones (ej. pdf, jpg)</label>
            @php $exts = old('extensiones_permitidas', $tipo?->extensiones_permitidas ?? ['pdf']); @endphp
            <input type="text" name="extensiones_permitidas[]" class="form-control" value="{{ is_array($exts) ? implode(', ', $exts) : $exts }}" placeholder="pdf">
            <small class="text-muted">Separadas por coma en un solo campo</small>
        </div>
        <div class="col-md-2 form-group pt-4">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="es_obligatorio" name="es_obligatorio" value="1" @checked(old('es_obligatorio', $tipo?->es_obligatorio ?? true))>
                <label class="custom-control-label" for="es_obligatorio">Obligatorio</label>
            </div>
        </div>
        <div class="col-md-2 form-group pt-4">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="permite_multiples" name="permite_multiples" value="1" @checked(old('permite_multiples', $tipo?->permite_multiples ?? false))>
                <label class="custom-control-label" for="permite_multiples">Permite varios</label>
            </div>
        </div>
        <div class="col-md-2 form-group pt-4">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="activo" name="activo" value="1" @checked(old('activo', $tipo?->activo ?? true))>
                <label class="custom-control-label" for="activo">Activo</label>
            </div>
        </div>
    </div>

    <div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('aitg.tipos-archivo.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
