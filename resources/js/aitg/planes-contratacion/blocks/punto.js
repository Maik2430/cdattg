/** Bloques dinámicos de puntos adicionales del plan AITG. */

/** Crea fila de punto adicional con descripción y puntaje. */
export function createPuntoBlock(container, onRemove, data = {}) {
    const index = container.querySelectorAll('.aitg-punto-block').length; // Índice nuevo
    const div = document.createElement('div'); // Contenedor fila
    div.className = 'row aitg-punto-block align-items-end'; // Clases wireframe

    div.innerHTML = `
        <div class="col-md-1"><span class="badge badge-warning aitg-bloque-numero">${index + 1}</span></div>
        <div class="col-md-6 form-group mb-1">
            <label class="small mb-0">Punto adicional</label>
            ${data.id ? `<input type="hidden" data-name-template="puntos_adicionales[__INDEX__][id]" name="puntos_adicionales[${index}][id]" value="${data.id}">` : ''}
            <input type="text" class="form-control" data-name-template="puntos_adicionales[__INDEX__][descripcion]"
                name="puntos_adicionales[${index}][descripcion]" value="${data.descripcion ?? ''}" placeholder="Descripción del criterio" required>
            <small class="form-text">Describa el criterio adicional que otorgará puntaje.</small>
        </div>
        <div class="col-md-3 form-group mb-1">
            <label class="small mb-0">Puntaje</label>
            <input type="number" step="0.01" min="0" class="form-control" data-name-template="puntos_adicionales[__INDEX__][puntaje_adicional]"
                name="puntos_adicionales[${index}][puntaje_adicional]" value="${data.puntaje_adicional ?? ''}" required>
            <small class="form-text">Valor del puntaje asignado.</small>
        </div>
        <div class="col-md-2 form-group mb-1 text-right">
            <button type="button" class="btn btn-sm btn-outline-danger aitg-remove-block"><i class="fas fa-trash"></i></button>
        </div>
    `;

    div.querySelector('.aitg-remove-block')?.addEventListener('click', () => onRemove(div)); // Eliminar fila

    return div; // Fila lista
}
