// ===== GLOBAL MODALS SYSTEM =====

// Sistema de modales global para todo el proyecto
console.log('🚀 Global modals script loaded');

// Variables globales para el modal
let modalConfig = null;
let modalElement = null;

// Función para mostrar el modal
window.showGlobalModal = function(config) {
    console.log('🔍 Showing modal with config:', config);
    modalConfig = config;
    modalElement = document.getElementById('globalConfirmModal');
    
    if (!modalElement) {
        console.error('❌ Modal element not found');
        return;
    }
    
    // Configurar contenido
    const titleElement = modalElement.querySelector('.modal-title');
    const messageElement = modalElement.querySelector('.modal-message');
    const iconElement = modalElement.querySelector('.modal-icon');
    const confirmBtn = modalElement.querySelector('.btn-confirm');
    
    if (!titleElement || !messageElement || !iconElement || !confirmBtn) {
        console.error('❌ Modal elements not found');
        return;
    }
    
    titleElement.textContent = config.title;
    messageElement.textContent = config.message;
    
    // Configurar icono según tipo
    iconElement.className = 'modal-icon fas fa-3x mb-3';
    switch(config.type) {
        case 'danger':
            iconElement.classList.add('text-danger');
            break;
        case 'warning':
            iconElement.classList.add('text-warning');
            break;
        case 'info':
            iconElement.classList.add('text-info');
            break;
        default:
            iconElement.classList.add('text-primary');
    }
    
    // Configurar botón de confirmación
    confirmBtn.className = 'btn btn-' + config.type;
    confirmBtn.textContent = getButtonText(config.type);
    
    // Mostrar modal
    modalElement.style.display = 'block';
    modalElement.classList.add('show');
    
    console.log('✅ Modal shown successfully');
};

// Función para cerrar el modal
window.closeGlobalModal = function() {
    console.log('🔍 Closing modal');
    if (modalElement) {
        modalElement.style.display = 'none';
        modalElement.classList.remove('show');
        modalConfig = null;
        console.log('✅ Modal closed successfully');
    }
};

// Función para confirmar acción
window.confirmGlobalModal = function() {
    console.log('🔍 Confirming action:', modalConfig);
    if (!modalConfig) return;
    
    // Enviar evento a Livewire
    if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('confirmAction', {
            action: modalConfig.action,
            params: modalConfig.params
        });
        console.log('✅ Livewire event dispatched');
    } else {
        console.error('❌ Livewire not found');
    }
    
    // Cerrar modal
    closeGlobalModal();
};

// Función auxiliar para texto del botón
function getButtonText(type) {
    switch(type) {
        case 'danger':
            return 'Eliminar';
        case 'warning':
            return 'Confirmar';
        case 'info':
            return 'Aceptar';
        default:
            return 'Aceptar';
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM loaded, setting up modal listeners');
    
    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeGlobalModal();
        }
    });
    
    // Cerrar modal al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (modalElement && e.target === modalElement) {
            closeGlobalModal();
        }
    });
    
    // Listener para eventos de Livewire
    if (typeof Livewire !== 'undefined') {
        Livewire.on('confirm', function(e) {
            console.log('🔍 Livewire confirm event received:', e.detail);
            showGlobalModal(e.detail);
        });
        console.log('✅ Livewire confirm listener setup complete');
    } else {
        console.error('❌ Livewire not available during setup');
    }
});
