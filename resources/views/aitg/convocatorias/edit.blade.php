@extends('adminlte::page')

@section('title', 'Editar convocatoria - AITG')

@section('css')
    <x-vite-stylesheet paths="resources/css/aitg/planes-contratacion/app.css" />
@endsection

@section('content_header')
    @include('aitg.planes-contratacion.partials.layout.page-header', [
        'title' => 'Editar convocatoria',
        'subtitle' => $convocatoria->codigo,
        'breadcrumb' => [
            ['label' => 'Convocatorias', 'url' => route('aitg.convocatorias.index'), 'icon' => 'fa-bullhorn'],
            ['label' => 'Editar', 'active' => true],
        ],
    ])
@endsection

@section('content')
<section class="content aitg-content mt-2">
    <div class="container-fluid">
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
        @include('aitg.convocatorias.partials.form-convocatoria', [
            'convocatoria' => $convocatoria,
            'action' => route('aitg.convocatorias.update', $convocatoria),
            'method' => 'PUT',
        ])
    </div>
</section>
@endsection

@section('js')
<script>
document.getElementById('competencia_id')?.addEventListener('change', function () {
    const planSelect = document.getElementById('plan_contratacion_id');
    if (!planSelect || !this.value) return;
    fetch('{{ route('aitg.convocatorias.planes-por-competencia') }}?competencia_id=' + this.value)
        .then(r => r.json())
        .then(items => {
            planSelect.innerHTML = '<option value="">Seleccione plan...</option>';
            items.forEach(i => { const o = document.createElement('option'); o.value = i.id; o.textContent = i.label; planSelect.appendChild(o); });
        });
});
</script>
@endsection
