/**
 * Script específico para formularios de competencias (create/edit)
 */
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 5000,
        alertSelector: '.alert'
    });
    
    // Función para aplicar borde rojo a Select2
    function aplicarBordeRojo($select) {
        // Buscar el contenedor de Select2 de múltiples formas
        let $container = $select.next('.select2-container');
        if (!$container.length) {
            $container = $select.parent().find('.select2-container');
        }
        if (!$container.length) {
            $container = $('body').find('.select2-container').last();
        }
        
        if ($container.length) {
            // Buscar todos los posibles elementos de selección
            const $selection = $container.find('.select2-selection');
            const $selectionSingle = $container.find('.select2-selection--single');
            const $selectionMultiple = $container.find('.select2-selection--multiple');
            
            // Aplicar a todos los elementos encontrados
            const elements = [];
            if ($selection.length) elements.push(...Array.from($selection));
            if ($selectionSingle.length) elements.push(...Array.from($selectionSingle));
            if ($selectionMultiple.length) elements.push(...Array.from($selectionMultiple));
            
            if (elements.length > 0) {
                elements.forEach(element => {
                    if (element) {
                        element.style.setProperty('border', '1px solid #dc3545', 'important');
                        element.style.setProperty('border-color', '#dc3545', 'important');
                        element.style.setProperty('border-width', '1px', 'important');
                        element.style.setProperty('border-style', 'solid', 'important');
                    }
                });
            } else {
                // Si no encontramos elementos, intentar aplicar directamente al contenedor
                if ($container.length && $container[0]) {
                    $container[0].style.setProperty('border', '1px solid #dc3545', 'important');
                }
            }
        }
    }

    // Función para remover borde rojo de Select2
    function removerBordeRojo($select) {
        const $container = $select.next('.select2-container');
        if ($container.length) {
            const $selection = $container.find('.select2-selection');
            const $selectionSingle = $container.find('.select2-selection--single');
            const $selectionMultiple = $container.find('.select2-selection--multiple');
            
            const elements = [];
            if ($selection.length) elements.push(...$selection);
            if ($selectionSingle.length) elements.push(...$selectionSingle);
            if ($selectionMultiple.length) elements.push(...$selectionMultiple);
            
            elements.forEach(element => {
                if (element) {
                    element.style.removeProperty('border');
                    element.style.removeProperty('border-color');
                    element.style.removeProperty('border-width');
                    element.style.removeProperty('border-style');
                }
            });
            
            if ($container.length && $container[0]) {
                $container[0].style.removeProperty('border');
            }
        }
    }

    // Inicializar Select2
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').each(function() {
            const $select = $(this);
            
            $select.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: $select.data('placeholder') || 'Seleccione una opción',
                allowClear: true,
                language: {
                    noResults: () => 'No se encontraron resultados',
                    searching: () => 'Buscando...'
                }
            });

            // Si ya tiene error, aplicar borde rojo
            if ($select.hasClass('is-invalid')) {
                setTimeout(() => aplicarBordeRojo($select), 100);
                setTimeout(() => aplicarBordeRojo($select), 300);
                setTimeout(() => aplicarBordeRojo($select), 500);
            }
            
            // Escuchar cuando Select2 se abre/cierra para reaplicar estilos
            $select.on('select2:open select2:close', function() {
                if ($(this).hasClass('is-invalid')) {
                    setTimeout(() => aplicarBordeRojo($(this)), 10);
                }
            });

            // Remover borde cuando se selecciona algo
            $select.on('select2:select select2:unselect', function() {
                const valores = $(this).val();
                if (valores && valores.length > 0) {
                    $(this).removeClass('is-invalid');
                    removerBordeRojo($(this));
                }
            });
        });
    }

    // Validación del formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', (event) => {
            let isValid = true;
            const errors = [];

            // Validar descripcion
            const descripcion = document.querySelector('#descripcion');
            if (descripcion && !descripcion.value.trim()) {
                isValid = false;
                descripcion.classList.add('is-invalid');
                errors.push('La norma o unidad de competencia es obligatoria.');
            } else if (descripcion) {
                descripcion.classList.remove('is-invalid');
            }

            // Validar codigo
            const codigo = document.querySelector('#codigo');
            if (codigo && !codigo.value.trim()) {
                isValid = false;
                codigo.classList.add('is-invalid');
                errors.push('El código de norma es obligatorio.');
            } else if (codigo) {
                codigo.classList.remove('is-invalid');
            }

            // Validar nombre
            const nombre = document.querySelector('#nombre');
            if (nombre && !nombre.value.trim()) {
                isValid = false;
                nombre.classList.add('is-invalid');
                errors.push('El nombre de la competencia es obligatorio.');
            } else if (nombre) {
                nombre.classList.remove('is-invalid');
            }

            // Validar duracion
            const duracion = document.querySelector('#duracion');
            if (duracion && (!duracion.value || parseFloat(duracion.value) < 1)) {
                isValid = false;
                duracion.classList.add('is-invalid');
                errors.push('La duración máxima es obligatoria y debe ser al menos 1 hora.');
            } else if (duracion) {
                duracion.classList.remove('is-invalid');
            }

            // Validar programas
            const programas = document.querySelector('#programas');
            if (programas) {
                const $programas = $(programas);
                const valores = $programas.val();
                
                if (!valores || valores.length === 0) {
                    isValid = false;
                    $programas.addClass('is-invalid');
                    // Aplicar borde rojo múltiples veces para asegurar
                    aplicarBordeRojo($programas);
                    setTimeout(() => aplicarBordeRojo($programas), 10);
                    setTimeout(() => aplicarBordeRojo($programas), 50);
                    setTimeout(() => aplicarBordeRojo($programas), 100);
                    setTimeout(() => aplicarBordeRojo($programas), 200);
                    setTimeout(() => aplicarBordeRojo($programas), 500);
                    errors.push('Debe seleccionar al menos un programa de formación.');
                } else {
                    $programas.removeClass('is-invalid');
                    removerBordeRojo($programas);
                }
            }

            if (!isValid) {
                event.preventDefault();
                const errorMessage = errors.length > 0 ? errors.join(' ') : 'Por favor complete todos los campos obligatorios.';
                if (alertHandler && alertHandler.showError) {
                    alertHandler.showError(errorMessage);
                } else {
                    alert(errorMessage);
                }
                // Scroll al primer campo con error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    }

    const firstInput = document.querySelector('input[type="text"], input[type="number"], textarea');
    if (firstInput && !firstInput.value) {
        firstInput.focus();
    }
});
