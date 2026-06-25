/** Bloques dinámicos del checklist de evaluación del plan AITG. */

/** Crea bloque de checklist con descripción del criterio. */
export function createChecklistBlock(container, onRemove, data = {}) {
    const index = container.querySelectorAll('.aitg-checklist-block').length;
    const div = document.createElement('div');
    div.className = 'card mb-3 aitg-checklist-block';

    div.innerHTML = `
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <div>
                <span class="badge badge-info aitg-bloque-numero mr-2">${index + 1}</span>
                <strong class="aitg-bloque-label">Checklist ${index + 1}</strong>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger aitg-remove-block"><i class="fas fa-trash"></i></button>
        </div>
        <div class="card-body py-2">
            ${data.id ? `<input type="hidden" data-name-template="checklist[__INDEX__][id]" name="checklist[${index}][id]" value="${data.id}">` : ''}
            <div class="form-group mb-0">
                <label class="small mb-1">Descripción del criterio <span class="text-danger">*</span></label>
                <textarea rows="2" class="form-control" data-name-template="checklist[__INDEX__][descripcion_criterio]"
                    name="checklist[${index}][descripcion_criterio]" required placeholder="Describa el criterio de evaluación para seleccionar a la persona...">${data.descripcion_criterio ?? ''}</textarea>
                <small class="form-text">Este criterio se usará para evaluar y seleccionar al candidato.</small>
            </div>
        </div>
    `;

    div.querySelector('.aitg-remove-block')?.addEventListener('click', () => onRemove(div));

    return div;
}

/** Renumera badges y labels del checklist. */
export function renumerarChecklist(container) {
    const blocks = container.querySelectorAll('.aitg-checklist-block');
    blocks.forEach((block, index) => {
        const badge = block.querySelector('.aitg-bloque-numero');
        const label = block.querySelector('.aitg-bloque-label');
        if (badge) {
            badge.textContent = index + 1;
        }
        if (label) {
            label.textContent = `Checklist ${index + 1}`;
        }
        block.querySelectorAll('[data-name-template]').forEach((input) => {
            const template = input.getAttribute('data-name-template');
            input.name = template.replace('__INDEX__', index);
        });
    });
}
