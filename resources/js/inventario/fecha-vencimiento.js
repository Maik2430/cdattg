
/**
 * Inicializa el comportamiento de fecha de vencimiento
 * Muestra/oculta el campo según si el producto es consumible
 */
export function initFechaVencimiento() {
    const tipoSelect = document.getElementById('tipo_producto_id');
    const fechaContainer = document.getElementById('fecha-vencimiento-container');
    const fechaInput = document.getElementById('fecha_vencimiento');

    if (!tipoSelect || !fechaContainer || !fechaInput) {
        console.warn('Elementos de fecha de vencimiento no encontrados');
        return;
    }

    /**
     * Alterna la visibilidad del campo de fecha de vencimiento
     */
    function toggleFechaVencimiento() {
        const selectedOption = tipoSelect.options[tipoSelect.selectedIndex];
        const tipoProducto = selectedOption.dataset?.tipo || '';
        
        // Mostrar solo si es consumible (no "no consumible")
        const esConsumible = tipoProducto === 'consumible' || 
                            (tipoProducto.includes('consumible') && !tipoProducto.includes('no consumible'));
        
        if (esConsumible) {
            fechaContainer.style.display = 'block';
            fechaInput.required = true;
        } else {
            fechaContainer.style.display = 'none';
            fechaInput.required = false;
        }
    }

    // Ejecutar al cambiar el select
    tipoSelect.addEventListener('change', toggleFechaVencimiento);
    
    // Ejecutar al cargar la página (por si hay valores old() o en edición)
    toggleFechaVencimiento();
}

/**
 * Inicializa el comportamiento de fecha de vencimiento para formulario de creación
 * Limpia el valor del campo cuando se oculta
 */
export function initFechaVencimientoCreate() {
    const tipoSelect = document.getElementById('tipo_producto_id');
    const fechaContainer = document.getElementById('fecha-vencimiento-container');
    const fechaInput = document.getElementById('fecha_vencimiento');

    if (!tipoSelect || !fechaContainer || !fechaInput) {
        console.warn('Elementos de fecha de vencimiento no encontrados');
        return;
    }

    function toggleFechaVencimiento() {
        const selectedOption = tipoSelect.options[tipoSelect.selectedIndex];
        const tipoProducto = selectedOption.dataset?.tipo || '';
        
        const esConsumible = tipoProducto === 'consumible' || 
                            (tipoProducto.includes('consumible') && !tipoProducto.includes('no consumible'));
        
        if (esConsumible) {
            fechaContainer.style.display = 'block';
            fechaInput.required = true;
        } else {
            fechaContainer.style.display = 'none';
            fechaInput.required = false;
            fechaInput.value = ''; // Limpiar valor en formulario de creación
        }
    }

    tipoSelect.addEventListener('change', toggleFechaVencimiento);
    toggleFechaVencimiento();
}

// Auto-inicializar si se detecta DOM cargado
if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        // Detectar si estamos en página de crear o editar basado en la URL
        const isCreatePage = globalThis.location?.pathname?.includes('/create');
        
        if (isCreatePage) {
            initFechaVencimientoCreate();
        } else {
            initFechaVencimiento();
        }
    });
}
