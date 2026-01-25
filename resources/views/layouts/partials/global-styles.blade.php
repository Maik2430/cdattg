{{-- CSS Globales para toda la aplicación - SIEMPRE CARGADOS --}}
{{-- CSS base de la aplicación --}}
@vite(['resources/css/style.css'])

{{-- Estilos específicos de módulos --}}
@vite(['resources/css/programas.css'])
@vite(['resources/css/red-conocimiento.css'])

{{-- Estilos adicionales que puedan existir --}}
@yield('global_styles')

<style>
/* Fix simple para AdminLTE - SOLO lo necesario */
.content-wrapper {
    min-height: auto !important;
}

.wrapper {
    min-height: unset !important;
    height: auto !important;
}
</style>
