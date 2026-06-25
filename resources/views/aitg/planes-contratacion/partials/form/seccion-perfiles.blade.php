{{-- Sección 2: perfiles dinámicos del plan --}}
<div class="aitg-card aitg-card--success">
    <div class="aitg-card__header">
        <div class="aitg-card__title-wrap">
            <span class="aitg-card__icon aitg-card__icon--success"><i class="fas fa-layer-group"></i></span>
            <h3 class="aitg-card__title" id="aitg-perfiles-section-title">Perfiles del plan</h3>
        </div>
        <button type="button" class="btn btn-sm aitg-btn-add aitg-btn-add--success" id="btn-add-perfil">
            <i class="fas fa-plus"></i> Agregar bloque
        </button>
    </div>
    <div class="aitg-card__body">
        <p class="aitg-card__hint mb-3">
            Agregue bloques según la forma seleccionada. Cada bloque incluye descripción del criterio y, opcionalmente, meses de experiencia.
        </p>
        <div id="aitg-perfiles-container">
            <div class="aitg-empty-state" id="aitg-perfiles-empty">No hay bloques de perfil. Use «Agregar bloque» para comenzar.</div>
        </div>
    </div>
</div>
