{{-- CSS globales: stylesheet directo en producción (sin preload innecesario) --}}
<x-vite-stylesheet paths="resources/css/style.css" />

@yield('global_styles')

<style>
.content-wrapper {
    min-height: auto !important;
}

.wrapper {
    min-height: unset !important;
    height: auto !important;
}
</style>
