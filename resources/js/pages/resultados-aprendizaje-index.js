document.addEventListener('DOMContentLoaded', function() {
    // Manejo de alertas
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // AJAX search con debounce
    let searchTimeout;
    const searchInput = document.querySelector('[wire\\:model\\.live\\.debounce\\.300ms="search"]');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Livewire maneja esto automáticamente con wire:model.live.debounce
            }, 300);
        });
    }

    // Manejo de filtros
    const filters = document.querySelectorAll('.filter-select, .results-select');
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            // Livewire maneja esto automáticamente con wire:model.live
        });
    });

    // URL state management
    function updateURL() {
        const url = new URL(window.location);
        const search = document.querySelector('[wire\\:model="search"]')?.value;
        const perPage = document.querySelector('[wire\\:model="perPage"]')?.value;
        const statusFilter = document.querySelector('[wire\\:model="statusFilter"]')?.value;
        const competenciaFilter = document.querySelector('[wire\\:model="competenciaFilter"]')?.value;
        
        if (search) url.searchParams.set('search', search);
        if (perPage) url.searchParams.set('perPage', perPage);
        if (statusFilter) url.searchParams.set('statusFilter', statusFilter);
        if (competenciaFilter) url.searchParams.set('competenciaFilter', competenciaFilter);
        
        window.history.replaceState({}, '', url);
    }

    // Observar cambios en los filtros
    const observer = new MutationObserver(updateURL);
    observer.observe(document.body, { childList: true, subtree: true });

    // Modal management
    window.addEventListener('closeModal', function() {
        const modals = document.querySelectorAll('.modal-overlay');
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
    });

    // Loading states
    const loadingIndicators = document.querySelectorAll('.loading-indicator');
    loadingIndicators.forEach(indicator => {
        if (indicator.textContent.trim() === '') {
            indicator.style.display = 'none';
        }
    });

    // Table actions
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-action')) {
            // Animación sutil para botones de acción
            const button = e.target.closest('.btn-action');
            button.style.transform = 'scale(0.95)';
            setTimeout(() => {
                button.style.transform = 'scale(1)';
            }, 100);
        }
    });

    // Badge toggle animations
    document.addEventListener('click', function(e) {
        if (e.target.closest('.badge-toggle')) {
            const badge = e.target.closest('.badge-toggle');
            const icon = badge.querySelector('i');
            
            if (icon) {
                icon.style.transform = 'rotate(180deg)';
                setTimeout(() => {
                    icon.style.transform = 'rotate(0deg)';
                }, 200);
            }
        }
    });

    // Form validation feedback
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';
            }
        });
    });

    // Modal overlay clicks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            e.target.style.display = 'none';
        }
    });

    // Escape key to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal-overlay');
            modals.forEach(modal => {
                modal.style.display = 'none';
            });
        }
    });

    // Initialize tooltips if needed
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
