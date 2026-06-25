/** Etiquetas dinámicas de bloques según tipo de registro AITG. */

/** Devuelve etiqueta de bloque (Opción, Alternativa o Registro). */
export function etiquetaBloque(tipo, index, total) {
    const n = index + 1; // Consecutivo visible (base 1)
    if (tipo === 'opcion') {
        return total === 1 ? 'Opción' : `Opción ${n}`; // Etiqueta opción
    }
    if (tipo === 'alternativa') {
        return total === 1 ? 'Alternativa' : `Alternativa ${n}`; // Etiqueta alternativa
    }
    return `Registro ${n}`; // Registro directo
}

/** Actualiza título de sección perfiles (wireframe: título fijo). */
export function tituloSeccionPerfiles() {
    const title = document.getElementById('aitg-perfiles-section-title'); // Elemento título
    if (title) {
        title.textContent = 'Perfiles del plan'; // Título fijo del wireframe
    }
}

/** Muestra u oculta estado vacío de un contenedor. */
export function toggleEmptyState(containerId, emptyId, selector) {
    const container = document.getElementById(containerId); // Contenedor de bloques
    const empty = document.getElementById(emptyId); // Mensaje sin datos
    if (! container || ! empty) {
        return; // Salir si faltan nodos
    }
    const hasBlocks = container.querySelectorAll(selector).length > 0; // Hay bloques
    empty.style.display = hasBlocks ? 'none' : 'block'; // Alternar visibilidad
}
