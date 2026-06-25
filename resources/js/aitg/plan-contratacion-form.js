/**
 * Formulario dinámico AITG - Plan de Contratación
 */
document.addEventListener('DOMContentLoaded', () => {
    const config = window.aitgPlanFormConfig;
    if (!config) return;

    const tipoSelect = document.getElementById('tipo_registro_perfil');
    const perfilesContainer = document.getElementById('aitg-perfiles-container');
    const puntosContainer = document.getElementById('aitg-puntos-container');
    const btnAddPerfil = document.getElementById('btn-add-perfil');
    const btnAddPunto = document.getElementById('btn-add-punto');
    const perfilesSectionTitle = document.getElementById('aitg-perfiles-section-title');

    const programasPorNivel = {};
    config.programas.forEach((p) => {
        const key = String(p.nivel_formacion_id);
        if (!programasPorNivel[key]) programasPorNivel[key] = [];
        programasPorNivel[key].push(p);
    });

    function etiquetaBloque(index, total) {
        const tipo = tipoSelect?.value || 'directo';
        const n = index + 1;
        if (tipo === 'opcion') return total === 1 ? 'Opción' : `Opción ${n}`;
        if (tipo === 'alternativa') return total === 1 ? 'Alternativa' : `Alternativa ${n}`;
        return `Registro ${n}`;
    }

    function tituloSeccion() {
        const tipo = tipoSelect?.value || 'directo';
        const map = {
            opcion: 'Perfiles del plan — registro por opción',
            alternativa: 'Perfiles del plan — registro por alternativa',
            directo: 'Perfiles del plan — nivel de formación y programa',
        };
        if (perfilesSectionTitle) {
            perfilesSectionTitle.textContent = map[tipo] || map.directo;
        }
    }

    function renumerarBloques(container, selector) {
        const blocks = container.querySelectorAll(selector);
        blocks.forEach((block, index) => {
            const label = block.querySelector('.aitg-bloque-label');
            const badge = block.querySelector('.aitg-bloque-numero');
            const total = blocks.length;
            if (label) label.textContent = etiquetaBloque(index, total);
            if (badge) badge.textContent = index + 1;
            block.querySelectorAll('[data-name-template]').forEach((input) => {
                const template = input.getAttribute('data-name-template');
                input.name = template.replace('__INDEX__', index);
            });
        });
    }

    function buildProgramaOptions(nivelId, selectedId) {
        const list = programasPorNivel[String(nivelId)] || [];
        let html = '<option value="">Seleccione programa...</option>';
        list.forEach((p) => {
            const sel = String(selectedId) === String(p.id) ? 'selected' : '';
            html += `<option value="${p.id}" ${sel}>${p.nombre} (${p.codigo})</option>`;
        });
        return html;
    }

    function attachNivelChange(block) {
        const nivelSelect = block.querySelector('.aitg-nivel-select');
        const programaSelect = block.querySelector('.aitg-programa-select');
        if (!nivelSelect || !programaSelect) return;

        nivelSelect.addEventListener('change', () => {
            programaSelect.innerHTML = buildProgramaOptions(nivelSelect.value, null);
        });
    }

    function createPerfilBlock(data = {}) {
        const index = perfilesContainer.querySelectorAll('.aitg-perfil-block').length;
        const div = document.createElement('div');
        div.className = 'card mb-3 aitg-perfil-block border-left-primary';
        div.innerHTML = `
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge badge-primary aitg-bloque-numero mr-2">${index + 1}</span>
                    <strong class="aitg-bloque-label">${etiquetaBloque(index, index + 1)}</strong>
                </div>
                <button type="button" class="btn btn-xs btn-outline-danger aitg-remove-block" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="card-body">
                ${data.id ? `<input type="hidden" data-name-template="perfiles[__INDEX__][id]" name="perfiles[${index}][id]" value="${data.id}">` : ''}
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nivel de formación <span class="text-danger">*</span></label>
                        <select class="form-control aitg-nivel-select" data-name-template="perfiles[__INDEX__][nivel_formacion_id]" name="perfiles[${index}][nivel_formacion_id]" required>
                            <option value="">Seleccione...</option>
                            ${config.niveles.map(n => `<option value="${n.id}" ${String(data.nivel_formacion_id) === String(n.id) ? 'selected' : ''}>${n.name}</option>`).join('')}
                        </select>
                        <small class="form-text text-muted">Seleccione el nivel de formación requerido para este perfil.</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Nombre del programa <span class="text-danger">*</span></label>
                        <select class="form-control aitg-programa-select" data-name-template="perfiles[__INDEX__][programa_formacion_id]" name="perfiles[${index}][programa_formacion_id]" required>
                            ${buildProgramaOptions(data.nivel_formacion_id, data.programa_formacion_id)}
                        </select>
                        <small class="form-text text-muted">Seleccione el programa correspondiente al perfil solicitado (Gestión Académica).</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Experiencia relacionada (meses) <span class="text-danger">*</span></label>
                        <input type="number" min="0" step="1" class="form-control" data-name-template="perfiles[__INDEX__][experiencia_relacionada_meses]"
                            name="perfiles[${index}][experiencia_relacionada_meses]" value="${data.experiencia_relacionada_meses ?? 0}" required>
                        <small class="form-text text-muted">Ingrese meses de experiencia relacionada. Solo números enteros.</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Experiencia en docencia (meses) <span class="text-danger">*</span></label>
                        <input type="number" min="0" step="1" class="form-control" data-name-template="perfiles[__INDEX__][experiencia_docencia_meses]"
                            name="perfiles[${index}][experiencia_docencia_meses]" value="${data.experiencia_docencia_meses ?? 0}" required>
                        <small class="form-text text-muted">Ingrese meses de experiencia en docencia. Solo números enteros.</small>
                    </div>
                </div>
            </div>
        `;

        div.querySelector('.aitg-remove-block')?.addEventListener('click', () => {
            div.remove();
            renumerarBloques(perfilesContainer, '.aitg-perfil-block');
        });

        attachNivelChange(div);
        return div;
    }

    function createPuntoBlock(data = {}) {
        const index = puntosContainer.querySelectorAll('.aitg-punto-block').length;
        const div = document.createElement('div');
        div.className = 'row aitg-punto-block align-items-end mb-2 border-bottom pb-2';
        div.innerHTML = `
            <div class="col-md-1"><span class="badge badge-secondary aitg-bloque-numero">${index + 1}</span></div>
            <div class="col-md-6 form-group mb-1">
                <label class="small mb-0">Punto adicional</label>
                ${data.id ? `<input type="hidden" data-name-template="puntos_adicionales[__INDEX__][id]" name="puntos_adicionales[${index}][id]" value="${data.id}">` : ''}
                <input type="text" class="form-control form-control-sm" data-name-template="puntos_adicionales[__INDEX__][descripcion]"
                    name="puntos_adicionales[${index}][descripcion]" value="${data.descripcion ?? ''}" placeholder="Descripción del criterio" required>
                <small class="form-text text-muted">Describa el criterio adicional que otorgará puntaje.</small>
            </div>
            <div class="col-md-3 form-group mb-1">
                <label class="small mb-0">Puntaje</label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm" data-name-template="puntos_adicionales[__INDEX__][puntaje_adicional]"
                    name="puntos_adicionales[${index}][puntaje_adicional]" value="${data.puntaje_adicional ?? ''}" required>
                <small class="form-text text-muted">Valor del puntaje asignado.</small>
            </div>
            <div class="col-md-2 form-group mb-1 text-right">
                <button type="button" class="btn btn-sm btn-outline-danger aitg-remove-block"><i class="fas fa-trash"></i></button>
            </div>
        `;

        div.querySelector('.aitg-remove-block')?.addEventListener('click', () => {
            div.remove();
            renumerarBloques(puntosContainer, '.aitg-punto-block');
        });

        return div;
    }

    btnAddPerfil?.addEventListener('click', () => {
        perfilesContainer.appendChild(createPerfilBlock());
        renumerarBloques(perfilesContainer, '.aitg-perfil-block');
    });

    btnAddPunto?.addEventListener('click', () => {
        puntosContainer.appendChild(createPuntoBlock());
        renumerarBloques(puntosContainer, '.aitg-punto-block');
    });

    tipoSelect?.addEventListener('change', () => {
        tituloSeccion();
        renumerarBloques(perfilesContainer, '.aitg-perfil-block');
    });

    (config.perfiles || []).forEach((p) => perfilesContainer.appendChild(createPerfilBlock(p)));
    (config.puntos || []).forEach((p) => puntosContainer.appendChild(createPuntoBlock(p)));

    tituloSeccion();
    renumerarBloques(perfilesContainer, '.aitg-perfil-block');
    renumerarBloques(puntosContainer, '.aitg-punto-block');
});
