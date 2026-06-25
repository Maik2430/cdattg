/** Sistema global de modales de confirmación (Livewire). */

let modalConfig = null;
let modalElement = null;

window.showGlobalModal = function (config) {
    modalConfig = config;
    modalElement = document.getElementById('globalConfirmModal');

    if (! modalElement) {
        return;
    }

    const titleElement = modalElement.querySelector('.modal-title');
    const messageElement = modalElement.querySelector('.modal-message');
    const iconElement = modalElement.querySelector('.modal-icon');
    const confirmBtn = modalElement.querySelector('.btn-confirm');

    if (! titleElement || ! messageElement || ! iconElement || ! confirmBtn) {
        return;
    }

    titleElement.textContent = config.title;
    messageElement.textContent = config.message;

    iconElement.className = 'modal-icon fas fa-3x mb-3';
    const iconTone = {
        danger: 'text-danger',
        warning: 'text-warning',
        info: 'text-info',
    };
    iconElement.classList.add(iconTone[config.type] || 'text-primary');

    confirmBtn.className = `btn btn-${config.type}`;
    confirmBtn.textContent = getButtonText(config.type);

    modalElement.style.display = 'block';
    modalElement.classList.add('show');
};

window.closeGlobalModal = function () {
    if (! modalElement) {
        return;
    }
    modalElement.style.display = 'none';
    modalElement.classList.remove('show');
    modalConfig = null;
};

window.confirmGlobalModal = function () {
    if (! modalConfig) {
        return;
    }

    if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('confirmAction', {
            action: modalConfig.action,
            params: modalConfig.params,
        });
    }

    closeGlobalModal();
};

function getButtonText(type) {
    const labels = {
        danger: 'Eliminar',
        warning: 'Confirmar',
        info: 'Aceptar',
    };

    return labels[type] || 'Aceptar';
}

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeGlobalModal();
        }
    });

    document.addEventListener('click', (event) => {
        if (modalElement && event.target === modalElement) {
            closeGlobalModal();
        }
    });

    if (typeof Livewire !== 'undefined') {
        Livewire.on('confirm', (event) => {
            showGlobalModal(event.detail);
        });
    }
});
