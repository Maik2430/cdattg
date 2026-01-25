<div class="modal-erp-container">
    <form wire:submit="save">
        <!-- Contenido principal -->
        <div class="modal-body-erp">
            <!-- Bloque 1 - Identidad -->
            <div class="section-block">
                <h6 class="section-title">Identidad del Resultado de Aprendizaje</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigo" class="form-label-erp">Código del Resultado</label>
                            <input type="text" 
                                   id="codigo"
                                   wire:model="codigo" 
                                   class="form-control-erp @error('codigo') is-invalid @enderror" 
                                   placeholder="Ej: 22050100101" 
                                   maxlength="20"
                                   required>
                            @error('codigo')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre" class="form-label-erp">Nombre del Resultado</label>
                            <input type="text" 
                                   id="nombre"
                                   wire:model="nombre" 
                                   class="form-control-erp @error('nombre') is-invalid @enderror" 
                                   placeholder="Ej: Analizar requerimientos del sistema" 
                                   maxlength="255"
                                   required>
                            @error('nombre')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bloque 2 - Duración y Competencia -->
            <div class="section-block">
                <h6 class="section-title">Asociación y Duración</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="competencia_id" class="form-label-erp">Competencia Asociada</label>
                            <select id="competencia_id"
                                    wire:model="competencia_id" 
                                    class="form-control-erp @error('competencia_id') is-invalid @enderror">
                                <option value="">Seleccionar competencia (opcional)</option>
                                @foreach($competencias as $competencia)
                                    <option value="{{ $competencia->id }}">{{ $competencia->codigo }} - {{ Str::limit($competencia->nombre, 40) }}</option>
                                @endforeach
                            </select>
                            @error('competencia_id')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="duracion" class="form-label-erp">Duración (horas)</label>
                            <div class="input-group-erp">
                                <input type="number" 
                                       id="duracion"
                                       wire:model="duracion" 
                                       class="form-control-erp @error('duracion') is-invalid @enderror" 
                                       placeholder="Ej: 120" 
                                       step="0.01"
                                       min="0"
                                       max="9999.99">
                                <span class="input-group-text-erp">horas</span>
                            </div>
                            @error('duracion')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bloque 3 - Estado -->
            <div class="section-block">
                <h6 class="section-title">Estado</h6>
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="status" wire:model="status">
                                <label class="form-check-label" for="status">
                                    <strong>Resultado de Aprendizaje Activo</strong>
                                    <span class="form-text">Los resultados activos pueden ser utilizados en guías de aprendizaje</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bloque 4 - Información Importante -->
            <div class="section-block section-info">
                <div class="info-content">
                    <i class="info-icon fas fa-info-circle"></i>
                    <div class="info-text">
                        <strong>Nota:</strong> El código se almacenará automáticamente en mayúsculas. 
                        La asociación a una competencia es opcional pero recomendada para una mejor organización.
                        Si se asocia a una competencia, la duración se redistribuirá automáticamente.
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer ERP -->
        <div class="modal-footer-erp">
            <div class="footer-actions">
                <button type="button" wire:click="cancel" class="btn-erp btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
                <button type="button" 
                        wire:click="save" 
                        class="btn-erp btn-primary">
                    <i class="fas fa-save"></i>
                    {{ $isEdit ? 'Actualizar' : 'Guardar' }} Resultado
                </button>
            </div>
        </div>
    </form>
</div>
