@props(['plan' => null, 'programas' => collect(), 'regionales' => collect()])

{{-- Sección 1: datos generales del plan --}}
<div class="aitg-card aitg-card--primary">
    <div class="aitg-card__header">
        <div class="aitg-card__title-wrap">
            <span class="aitg-card__icon aitg-card__icon--primary"><i class="fas fa-file-alt"></i></span>
            <h3 class="aitg-card__title">Datos generales del plan</h3>
        </div>
    </div>
    <div class="aitg-card__body">
        <div class="row">
            <div class="col-lg-6 form-group">
                <label for="programa_formacion_id">Nombre del programa <span class="text-danger">*</span></label>
                <select name="programa_formacion_id" id="programa_formacion_id"
                    class="form-control @error('programa_formacion_id') is-invalid @enderror" required>
                    <option value="">Seleccione el programa principal...</option>
                    @foreach($programas as $programa)
                        <option value="{{ $programa->id }}" @selected(old('programa_formacion_id', $plan?->programa_formacion_id) == $programa->id)>
                            {{ $programa->nombre }} ({{ $programa->codigo }})
                        </option>
                    @endforeach
                </select>
                <small class="form-text">Seleccione el programa principal asociado al plan (Gestión Académica).</small>
                @error('programa_formacion_id')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="modalidad">Modalidad <span class="text-danger">*</span></label>
                <select name="modalidad" id="modalidad" class="form-control @error('modalidad') is-invalid @enderror" required>
                    @foreach(\App\Models\Aitg\PlanContratacion::MODALIDADES as $value => $label)
                        <option value="{{ $value }}" @selected(old('modalidad', $plan?->modalidad) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('modalidad')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="regional_id">Regional <span class="text-danger">*</span></label>
                <select name="regional_id" id="regional_id" class="form-control @error('regional_id') is-invalid @enderror" required>
                    <option value="">Seleccione...</option>
                    @foreach($regionales as $regional)
                        <option value="{{ $regional->id }}" @selected(old('regional_id', $plan?->regional_id) == $regional->id)>{{ $regional->nombre }}</option>
                    @endforeach
                </select>
                @error('regional_id')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 form-group">
                <label for="tipo_registro_perfil">Forma de registro del perfil <span class="text-danger">*</span></label>
                <select name="tipo_registro_perfil" id="tipo_registro_perfil"
                    class="form-control @error('tipo_registro_perfil') is-invalid @enderror" required>
                    @foreach(\App\Models\Aitg\PlanContratacion::TIPOS_REGISTRO_PERFIL as $value => $label)
                        <option value="{{ $value }}" @selected(old('tipo_registro_perfil', $plan?->tipo_registro_perfil ?? 'directo') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <small class="form-text">Define si el perfil se registra por opción, alternativa o directamente por nivel y programa.</small>
                @error('tipo_registro_perfil')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
            </div>
            <div class="col-md-2 form-group">
                <label for="periodo">Período <span class="text-danger">*</span></label>
                <input type="text" name="periodo" id="periodo" class="form-control @error('periodo') is-invalid @enderror"
                    placeholder="2026-1" value="{{ old('periodo', $plan?->periodo) }}" required>
                @error('periodo')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
            </div>
            <div class="col-md-2 form-group">
                <label for="fecha_inicio">Fecha inicio <span class="text-danger">*</span></label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror"
                    value="{{ old('fecha_inicio', $plan?->fecha_inicio?->format('Y-m-d')) }}" required>
                @error('fecha_inicio')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
            </div>
            <div class="col-md-2 form-group">
                <label for="fecha_fin">Fecha fin <span class="text-danger">*</span></label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror"
                    value="{{ old('fecha_fin', $plan?->fecha_fin?->format('Y-m-d')) }}" required>
                @error('fecha_fin')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
            </div>
            <div class="col-md-3 form-group">
                <label for="estado">Estado <span class="text-danger">*</span></label>
                <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                    @foreach(\App\Models\Aitg\PlanContratacion::ESTADOS as $value => $label)
                        <option value="{{ $value }}" @selected(old('estado', $plan?->estado ?? 'borrador') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <small class="form-text">Condición actual del registro en el sistema.</small>
                @error('estado')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 form-group">
                <label for="tope_global">Tope global</label>
                <input type="number" step="0.01" min="0" name="tope_global" id="tope_global"
                    class="form-control @error('tope_global') is-invalid @enderror"
                    placeholder="—" value="{{ old('tope_global', $plan?->tope_global) }}">
                @error('tope_global')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
            </div>
            <div class="col-md-9 form-group">
                <label for="observaciones">Observaciones</label>
                <textarea name="observaciones" id="observaciones" rows="2"
                    class="form-control @error('observaciones') is-invalid @enderror"
                    placeholder="Escribe observaciones del plan...">{{ old('observaciones', $plan?->observaciones) }}</textarea>
                @error('observaciones')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
</div>
