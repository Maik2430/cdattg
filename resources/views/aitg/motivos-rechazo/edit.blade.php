@extends('adminlte::page')

@section('title', 'Editar motivo de rechazo')

@section('content_header')<h1>Editar motivo de rechazo</h1>@endsection

@section('content')
<div class="container-fluid">
    @include('aitg.motivos-rechazo.partials.form', [
        'action' => route('aitg.motivos-rechazo.update', $motivo),
        'method' => 'PUT',
        'motivo' => $motivo,
    ])
</div>
@endsection
