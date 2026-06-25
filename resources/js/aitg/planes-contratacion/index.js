/**
 * Formulario dinámico AITG — Plan de Contratación.
 */
import { tituloSeccionPerfiles, toggleEmptyState } from './helpers/labels.js';
import { renumerarBloques } from './helpers/dom.js';
import { createPerfilBlock, syncTipoRegistroEnBloques } from './blocks/perfil.js';
import { createPuntoBlock } from './blocks/punto.js';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.aitgPlanFormConfig;
    if (! config) {
        return;
    }

    const tipoSelect = document.getElementById('tipo_registro_perfil');
    const perfilesContainer = document.getElementById('aitg-perfiles-container');
    const puntosContainer = document.getElementById('aitg-puntos-container');
    const btnAddPerfil = document.getElementById('btn-add-perfil');
    const btnAddPunto = document.getElementById('btn-add-punto');

    const getTipo = () => tipoSelect?.value || config.tipoRegistro || 'directo';

    const refreshPerfiles = () => {
        renumerarBloques(perfilesContainer, '.aitg-perfil-block', getTipo());
        toggleEmptyState('aitg-perfiles-container', 'aitg-perfiles-empty', '.aitg-perfil-block');
    };

    const refreshPuntos = () => {
        renumerarBloques(puntosContainer, '.aitg-punto-block', getTipo());
        toggleEmptyState('aitg-puntos-container', 'aitg-puntos-empty', '.aitg-punto-block');
    };

    const addPerfil = (data = {}) => {
        const block = createPerfilBlock(perfilesContainer, getTipo(), (node) => {
            node.remove();
            refreshPerfiles();
        }, data);
        perfilesContainer.appendChild(block);
        refreshPerfiles();
    };

    const addPunto = (data = {}) => {
        const block = createPuntoBlock(puntosContainer, (node) => {
            node.remove();
            refreshPuntos();
        }, data);
        puntosContainer.appendChild(block);
        refreshPuntos();
    };

    btnAddPerfil?.addEventListener('click', () => addPerfil());
    btnAddPunto?.addEventListener('click', () => addPunto());

    tipoSelect?.addEventListener('change', () => {
        tituloSeccionPerfiles();
        syncTipoRegistroEnBloques(perfilesContainer, getTipo());
        refreshPerfiles();
    });

    (config.perfiles || []).forEach((perfil) => addPerfil(perfil));
    (config.puntos || []).forEach((punto) => addPunto(punto));

    tituloSeccionPerfiles();
    refreshPerfiles();
    refreshPuntos();
});
