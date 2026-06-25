@props([
    'plan' => null,
    'regionales' => collect(),
    'nivelesFormacion' => collect(),
    'competencias' => collect(),
    'submitLabel' => 'Guardar plan',
    'formAction' => '',
    'formMethod' => 'POST',
    'aitgFormConfig' => [],
])

@php
    $plan = $plan ?? null;
    $isEdit = $formMethod === 'PUT';
@endphp

<form action="{{ $formAction }}" method="POST" id="aitg-plan-form" class="aitg-form">
    @csrf
    @if($isEdit) @method('PUT') @endif

    @include('aitg.planes-contratacion.partials.form.datos-generales', [
        'plan' => $plan,
        'competencias' => $competencias,
        'regionales' => $regionales,
    ])

    @include('aitg.planes-contratacion.partials.form.seccion-perfiles')
    @include('aitg.planes-contratacion.partials.form.seccion-puntos')

    @include('aitg.planes-contratacion.partials.form.acciones-formulario', [
        'submitLabel' => $submitLabel,
    ])
</form>

@push('js')
<script>
    window.aitgPlanFormConfig = @json($aitgFormConfig);
</script>
@vite(['resources/js/aitg/planes-contratacion/index.js'])
@endpush
