<div class="modal-erp-container">
    <form wire:submit="save">
        <!-- Contenido principal -->
        <div class="modal-body-erp">
            <!-- Bloque 1 - Identidad -->
            <div class="section-block">
                <h6 class="section-title">Identidad de la Competencia</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigo" class="form-label-erp">Código de la Competencia</label>
                            <input type="text" 
                                   id="codigo"
                                   wire:model="codigo" 
                                   class="form-control-erp @error('codigo') is-invalid @enderror" 
                                   placeholder="Ej: 220501001" 
                                   maxlength="20"
                                   required>
                            @error('codigo')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre" class="form-label-erp">Nombre de la Competencia</label>
                            <input type="text" 
                                   id="nombre"
                                   wire:model="nombre" 
                                   class="form-control-erp @error('nombre') is-invalid @enderror" 
                                   placeholder="Ej: Diseño y Desarrollo de Software" 
                                   maxlength="255"
                                   required>
                            @error('nombre')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descripcion" class="form-label-erp">Descripción</label>
                            <textarea id="descripcion"
                                      wire:model="descripcion" 
                                      class="form-control-erp @error('descripcion') is-invalid @enderror" 
                                      placeholder="Describe brevemente la competencia..." 
                                      rows="3"
                                      maxlength="500"
                                      required></textarea>
                            @error('descripcion')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bloque 2 - Duración y Fechas -->
            <div class="section-block">
                <h6 class="section-title">Duración y Vigencia</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="duracion" class="form-label-erp">Duración (horas)</label>
                            <div class="input-group-erp">
                                <input type="number" 
                                       id="duracion"
                                       wire:model="duracion" 
                                       class="form-control-erp @error('duracion') is-invalid @enderror" 
                                       placeholder="Ej: 1000" 
                                       step="0.01"
                                       min="0"
                                       max="9999.99"
                                       required>
                                <span class="input-group-text-erp">horas</span>
                            </div>
                            @error('duracion')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_inicio" class="form-label-erp">Fecha de Inicio</label>
                            <input type="date" 
                                   id="fecha_inicio"
                                   wire:model="fecha_inicio" 
                                   class="form-control-erp @error('fecha_inicio') is-invalid @enderror">
                            @error('fecha_inicio')
                                <div class="invalid-feedback-erp">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_fin" class="form-label-erp">Fecha de Fin</label>
                            <input type="date" 
                                   id="fecha_fin"
                                   wire:model="fecha_fin" 
                                   class="form-control-erp @error('fecha_fin') is-invalid @enderror">
                            @error('fecha_fin')
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
                                    <strong>Competencia Activa</strong>
                                    <span class="form-text">Las competencias activas pueden ser asignadas a programas</span>
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
                        Las fechas de vigencia son opcionales pero recomendadas para un mejor control de la competencia.
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
                    {{ $isEdit ? 'Actualizar' : 'Guardar' }} Competencia
                </button>
            </div>
        </div>
    </form>
</div>
