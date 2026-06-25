/** Utilidades DOM para renumerar campos dinámicos del formulario AITG. */
import { etiquetaBloque } from './labels.js';

/** Renumera índices, etiquetas y atributos name de bloques dinámicos. */
export function renumerarBloques(container, selector, tipoRegistro) {
    const blocks = container.querySelectorAll(selector);
    blocks.forEach((block, index) => {
        const label = block.querySelector('.aitg-bloque-label');
        const badge = block.querySelector('.aitg-bloque-numero');
        const total = blocks.length;
        if (label) {
            label.textContent = etiquetaBloque(tipoRegistro, index, total);
        }
        if (badge) {
            badge.textContent = index + 1;
        }
        block.querySelectorAll('[data-name-template]').forEach((input) => {
            const template = input.getAttribute('data-name-template');
            input.name = template.replace('__INDEX__', index);
        });
        const checkbox = block.querySelector('.aitg-incluye-exp');
        if (checkbox) {
            checkbox.id = `incluye-exp-${index}`;
            const checkboxLabel = block.querySelector(`label[for^="incluye-exp-"]`);
            if (checkboxLabel) {
                checkboxLabel.setAttribute('for', checkbox.id);
            }
        }
    });
}
