/** Bloques dinámicos de perfiles del plan AITG (descripción de criterio + experiencia opcional). */
import { etiquetaBloque } from '../helpers/labels.js';

/** Etiquetas de campos según forma de registro del plan. */
function etiquetasCampos(tipoRegistro) {
    if (tipoRegistro === 'directo') {
        return {
            principal: 'Descripción del criterio (nivel de formación)',
            secundaria: 'Descripción del criterio (programa)',
            placeholderPrincipal: 'Ej.: Ingeniero agrónomo, administrador de empresas agropecuarias...',
            placeholderSecundaria: 'Ej.: Tecnólogo en administración de empresas agropecuarias...',
        };
    }
    if (tipoRegistro === 'alternativa') {
        return {
            principal: 'Descripción del criterio (alternativa)',
            secundaria: null,
            placeholderPrincipal: 'Ej.: Tecnólogo en áreas de cocina, gastronomía y gestión hotelera...',
            placeholderSecundaria: '',
        };
    }
    return {
        principal: 'Descripción del criterio (opción)',
        secundaria: null,
        placeholderPrincipal: 'Ej.: Técnico área ocupacional 8381 - Mecánico vehículos automotores...',
        placeholderSecundaria: '',
    };
}

/** Vincula checkbox de experiencia con campos de meses. */
function attachExperienciaToggle(block) {
    const checkbox = block.querySelector('.aitg-incluye-exp');
    const panel = block.querySelector('.aitg-exp-panel');
    if (! checkbox || ! panel) {
        return;
    }

    const sync = () => {
        panel.classList.toggle('d-none', ! checkbox.checked);
        panel.querySelectorAll('input').forEach((input) => {
            input.disabled = ! checkbox.checked;
            if (! checkbox.checked) {
                input.value = '0';
            }
        });
    };

    checkbox.addEventListener('change', sync);
    sync();
}

/** Crea bloque de perfil con criterios en texto libre. */
export function createPerfilBlock(container, tipoRegistro, onRemove, data = {}) {
    const index = container.querySelectorAll('.aitg-perfil-block').length;
    const labels = etiquetasCampos(tipoRegistro);
    const esDirecto = tipoRegistro === 'directo';
    const incluyeExp = Boolean(data.incluye_experiencia);

    const div = document.createElement('div');
    div.className = 'card aitg-perfil-block';
    div.innerHTML = `
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <div>
                <span class="badge badge-success aitg-bloque-numero mr-2">${index + 1}</span>
                <strong class="aitg-bloque-label">${etiquetaBloque(tipoRegistro, index, index + 1)}</strong>
            </div>
            <button type="button" class="btn btn-xs btn-outline-danger aitg-remove-block" title="Eliminar">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="card-body">
            ${data.id ? `<input type="hidden" data-name-template="perfiles[__INDEX__][id]" name="perfiles[${index}][id]" value="${data.id}">` : ''}
            <div class="form-group">
                <label>${labels.principal} <span class="text-danger">*</span></label>
                <textarea rows="2" class="form-control aitg-desc-principal" data-name-template="perfiles[__INDEX__][descripcion_criterio]"
                    name="perfiles[${index}][descripcion_criterio]" required placeholder="${labels.placeholderPrincipal}">${data.descripcion_criterio ?? ''}</textarea>
            </div>
            ${esDirecto ? `
            <div class="form-group aitg-campo-programa">
                <label>${labels.secundaria} <span class="text-danger">*</span></label>
                <textarea rows="2" class="form-control" data-name-template="perfiles[__INDEX__][descripcion_criterio_programa]"
                    name="perfiles[${index}][descripcion_criterio_programa]" required placeholder="${labels.placeholderSecundaria}">${data.descripcion_criterio_programa ?? ''}</textarea>
            </div>` : ''}
            <div class="form-group mb-2">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input aitg-incluye-exp" id="incluye-exp-${index}"
                        data-name-template="perfiles[__INDEX__][incluye_experiencia]" name="perfiles[${index}][incluye_experiencia]" value="1"
                        ${incluyeExp ? 'checked' : ''}>
                    <label class="custom-control-label" for="incluye-exp-${index}">¿Desea agregar meses de experiencia?</label>
                </div>
            </div>
            <div class="aitg-exp-panel row ${incluyeExp ? '' : 'd-none'}">
                <div class="col-md-6 form-group">
                    <label>Experiencia relacionada (meses)</label>
                    <input type="number" min="0" step="1" class="form-control" data-name-template="perfiles[__INDEX__][experiencia_relacionada_meses]"
                        name="perfiles[${index}][experiencia_relacionada_meses]" value="${data.experiencia_relacionada_meses ?? 0}" ${incluyeExp ? '' : 'disabled'}>
                </div>
                <div class="col-md-6 form-group">
                    <label>Experiencia en docencia (meses)</label>
                    <input type="number" min="0" step="1" class="form-control" data-name-template="perfiles[__INDEX__][experiencia_docencia_meses]"
                        name="perfiles[${index}][experiencia_docencia_meses]" value="${data.experiencia_docencia_meses ?? 0}" ${incluyeExp ? '' : 'disabled'}>
                </div>
            </div>
        </div>
    `;

    attachExperienciaToggle(div);
    div.querySelector('.aitg-remove-block')?.addEventListener('click', () => onRemove(div));

    return div;
}

/** Actualiza visibilidad del campo programa al cambiar tipo de registro. */
export function syncTipoRegistroEnBloques(container, tipoRegistro) {
    container.querySelectorAll('.aitg-perfil-block').forEach((block, index) => {
        const labels = etiquetasCampos(tipoRegistro);
        const labelPrincipal = block.querySelector('.aitg-desc-principal')?.previousElementSibling;
        if (labelPrincipal) {
            labelPrincipal.innerHTML = `${labels.principal} <span class="text-danger">*</span>`;
        }

        const campoPrograma = block.querySelector('.aitg-campo-programa');
        if (tipoRegistro === 'directo' && ! campoPrograma) {
            const textarea = block.querySelector('.aitg-desc-principal');
            const grupo = document.createElement('div');
            grupo.className = 'form-group aitg-campo-programa';
            grupo.innerHTML = `
                <label>${labels.secundaria} <span class="text-danger">*</span></label>
                <textarea rows="2" class="form-control" data-name-template="perfiles[__INDEX__][descripcion_criterio_programa]"
                    name="perfiles[${index}][descripcion_criterio_programa]" required placeholder="${labels.placeholderSecundaria}"></textarea>
            `;
            textarea?.closest('.form-group')?.after(grupo);
        } else if (tipoRegistro !== 'directo' && campoPrograma) {
            campoPrograma.remove();
        }
    });
}
