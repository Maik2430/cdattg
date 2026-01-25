<div class="modal-erp-container">
    <form wire:submit="save">
        <!-- Contenido principal -->
        <div class="modal-body-erp">
            <!-- Bloque 1 - Identidad -->
            <div class="section-block">
                <h6 class="section-title">Identidad de la Red</h6>
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nombre" class="form-label-erp">Nombre de la Red de Conocimiento</label>
                            <input type="text" 
                                   id="nombre"
                                   wire:model="nombre" 
                                   class="form-control-erp @error('nombre') is-invalid @enderror" 
                                   placeholder="Ej: TECNOLOGÍAS DE LA INFORMACIÓN" 
                                   maxlength="255"
                                   required>
                            @error('nombre')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bloque 2 - Ubicación -->
            <div class="section-block">
                <h6 class="section-title">Ubicación</h6>
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="regionals_id" class="form-label-erp">Regional</label>
                            <select id="regionals_id"
                                    wire:model="regionals_id" 
                                    class="form-control-erp @error('regionals_id') is-invalid @enderror" 
                                    required>
                                <option value="">Seleccione una regional</option>
                                @foreach ($this->regionales as $regional)
                                    <option value="{{ $regional->id }}">
                                        {{ $regional->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('regionals_id')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bloque 3 - Información Importante -->
            <div class="section-block section-info">
                <div class="info-content">
                    <i class="info-icon fas fa-info-circle"></i>
                    <div class="info-text">
                        <strong>Nota:</strong> El nombre de la red se almacenará automáticamente en mayúsculas. 
                        Esta red podrá ser utilizada para organizar los programas de formación por áreas del conocimiento.
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
                    {{ $isEdit ? 'Actualizar' : 'Guardar' }} Red
                </button>
            </div>
        </div>
    </form>
</div>

