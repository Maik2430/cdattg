@extends('adminlte::page')

@section('title', 'Editar Plan - AITG')

@section('css')
    <x-vite-stylesheet paths="resources/css/aitg/planes-contratacion/app.css" />
@endsection

@section('content_header')
    @include('aitg.planes-contratacion.partials.layout.page-header', [
        'title' => 'Editar Plan de Contratación',
        'subtitle' => 'AITG - Anexo 2 · Formulario dinámico',
        'breadcrumb' => [
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Planes', 'url' => route('aitg.planes-contratacion.index'), 'icon' => 'fa-file-contract'],
            ['label' => 'Editar plan', 'active' => true],
        ],
    ])
@endsection

@section('content')
<section class="content aitg-content mt-2">
    <div class="container-fluid">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @include('aitg.planes-contratacion.partials.form-completo', [
            'plan' => $plan,
            'formAction' => route('aitg.planes-contratacion.update', $plan),
            'formMethod' => 'PUT',
            'submitLabel' => 'Guardar cambios',
            'regionales' => $regionales,
            'nivelesFormacion' => $nivelesFormacion,
            'programas' => $programas,
            'aitgFormConfig' => $aitgFormConfig,
        ])
    </div>
</section>
@endsection

@section('js')
    @stack('js')
@endsection
