/** Sistema global de notificaciones (Livewire + fallback manual). */

let listenerSetup = false;

/** Muestra toast sanitizado en #notify-container. */
function showGlobalNotify(message, type = 'success') {
    if (typeof message !== 'string') {
        message = String(message);
    }

    message = message.replace(/<[^>]*>/g, '').trim();

    if (message.length > 200) {
        message = `${message.substring(0, 200)}...`;
    }

    const container = document.getElementById('notify-container');
    if (! container) {
        return;
    }

    const div = document.createElement('div');
    div.className = `notify notify-${type}`;
    div.textContent = message;
    container.appendChild(div);

    requestAnimationFrame(() => {
        div.style.opacity = '1';
        div.style.transform = 'translateX(0)';
    });

    setTimeout(() => {
        div.style.opacity = '0';
        div.style.transform = 'translateX(100%)';
        setTimeout(() => div.remove(), 200);
    }, 2500);
}

/** Registra listener Livewire notify (una sola vez). */
function setupLivewireListener() {
    if (listenerSetup || typeof Livewire === 'undefined') {
        return;
    }

    let lastNotifyTime = 0;

    Livewire.on('notify', (data) => {
        const currentTime = Date.now();
        if (currentTime - lastNotifyTime < 100) {
            return;
        }
        lastNotifyTime = currentTime;

        let message = 'Notificación';
        let type = 'success';

        if (typeof data === 'string') {
            try {
                const parsed = JSON.parse(data);
                message = parsed.message || parsed.detail?.message || message;
                type = parsed.type || parsed.detail?.type || type;
            } catch {
                /* usar valores por defecto */
            }
        } else if (data && typeof data === 'object') {
            message = data.message || data.detail?.message || data[0]?.message || message;
            type = data.type || data.detail?.type || data[0]?.type || type;
        }

        showGlobalNotify(message, type);
    });

    listenerSetup = true;
}

if (window.Livewire) {
    setupLivewireListener();
} else {
    document.addEventListener('livewire:init', setupLivewireListener);
}

window.showNotify = showGlobalNotify;
window.showSuccess = (message) => showGlobalNotify(message, 'success');
window.showError = (message) => showGlobalNotify(message, 'error');
window.showWarning = (message) => showGlobalNotify(message, 'warning');
