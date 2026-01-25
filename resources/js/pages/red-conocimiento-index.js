/**
 * Script específico para la página de índice de redes de conocimiento
 */
import { TableActionsHandler } from '../modules/table-actions.js';
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar manejador de acciones de tabla
    const tableHandler = new TableActionsHandler('body', {
        deleteSelector: '.formulario-eliminar',
        tooltipSelector: '[data-toggle="tooltip"]',
        alertSelector: '.alert',
        autoHideAlerts: true,
        alertHideDelay: 5000
    });
    
    // Inicializar manejador de alertas
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 5000,
        alertSelector: '.alert'
    });
    
    let searchTimeout;
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Búsqueda en tiempo real con debounce
    $('#searchRed').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            performAjaxSearch();
        }, 500);
    });

    // Botón de búsqueda
    $('#btnSearch').on('click', function() {
        performAjaxSearch();
    });

    // Función de búsqueda AJAX
    function performAjaxSearch() {
        const searchTerm = $('#searchRed').val();
        const regional = $('#filterRegional').val();
        const estado = $('#filterEstado').val();

        // Construir URL con parámetros
        let url = window.location.pathname + '?';
        const params = [];

        if (searchTerm) params.push(`search=${encodeURIComponent(searchTerm)}`);
        if (regional) params.push(`regional_id=${regional}`);
        if (estado !== '') params.push(`estado=${estado}`);

        if (params.length > 0) {
            url += params.join('&');
        }

        // Realizar búsqueda AJAX
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                // Actualizar solo la tabla
                const $newTable = $(response).find('.table-responsive');
                $('.table-responsive').replaceWith($newTable);
                
                // Reinicializar tooltips en la nueva tabla
                $('[data-toggle="tooltip"]').tooltip();
                
                // Actualizar URL sin recargar la página
                window.history.pushState({}, '', url);
            },
            error: function() {
                alertHandler.showError('Error al realizar la búsqueda');
            }
        });
    }

    // Filtros
    $('#filterRegional, #filterEstado').on('change', function() {
        performAjaxSearch();
    });

    // Limpiar filtros
    $('#btnClearFilters').on('click', function() {
        $('#searchRed').val('');
        $('#filterRegional').val('');
        $('#filterEstado').val('');
        window.location.href = window.location.pathname;
    });

    // Auto-focus en el campo de búsqueda si está vacío
    if (!$('#searchRed').val()) {
        $('#searchRed').focus();
    }

    // Mostrar filtros si hay parámetros activos
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search') || urlParams.has('regional_id') || urlParams.has('estado')) {
        $('#filtrosCollapse').collapse('show');
    }

    // Cambiar ícono del chevron al expandir/colapsar
    $('#filtrosCollapse').on('show.bs.collapse', function () {
        $('[data-target="#filtrosCollapse"] .fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    $('#filtrosCollapse').on('hide.bs.collapse', function () {
        $('[data-target="#filtrosCollapse"] .fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // Restaurar valores de filtros desde URL
    if (urlParams.has('search')) $('#searchRed').val(urlParams.get('search'));
    if (urlParams.has('regional_id')) $('#filterRegional').val(urlParams.get('regional_id'));
    if (urlParams.has('estado')) $('#filterEstado').val(urlParams.get('estado'));
    
    console.log('Página de redes de conocimiento inicializada correctamente');
});
